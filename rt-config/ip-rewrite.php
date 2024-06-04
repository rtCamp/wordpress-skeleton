<?php

/**
 * Checks and validates whether the request is from Cloudflare or not.
 */
class rt_IP_Rewrite {

	private static $original_ip = null;

    	// Found at https://www.cloudflare.com/ips/
	private static $cf_ipv4 = array(
		'173.245.48.0/20',
		'103.21.244.0/22',
		'103.22.200.0/22',
		'103.31.4.0/22',
		'141.101.64.0/18',
		'108.162.192.0/18',
		'190.93.240.0/20',
		'188.114.96.0/20',
		'197.234.240.0/22',
		'198.41.128.0/17',
		'162.158.0.0/15',
		'104.16.0.0/12',
		'172.64.0.0/13',
		'131.0.72.0/22',
	);

	private static $cf_ipv6 = array(
		'2400:cb00::/32',
		'2405:8100::/32',
		'2405:b500::/32',
		'2606:4700::/32',
		'2803:f800::/32',
		'2c0f:f248::/32',
		'2a06:98c0::/29'
	);

	/**
	 * Is a request from CloudFlare?
	 * @return bool
	 */
	public static function isCloudFlare() {
		if ( ! isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			return false;
		} else {
			// Check if original ip has already been restored, e.g. by nginx - assume it was from cloudflare then
			if ( $_SERVER['REMOTE_ADDR'] === $_SERVER['HTTP_CF_CONNECTING_IP'] ) {
				return true;
			}
		}

		return self::isCloudFlareIP();
	}

	/**
	 * Check if a request comes from a CloudFlare IP.
	 * @return bool
	 */
	public static function isCloudFlareIP() {
		// Store original remote address in $original_ip
		self::$original_ip = self::getOriginalIP();
		if ( ! isset( self::$original_ip ) ) {
			return false;
		}

		// Process original_ip if on cloudflare
		$ip_ranges = self::$cf_ipv4;
		if ( self::isIpv6( self::$original_ip ) ) {
			$ip_ranges = self::$cf_ipv6;
		}

		foreach ( $ip_ranges as $range ) {
			if ( self::checkIp( self::$original_ip, $range ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the original IP Address of a given request.
	 * @return IP Address or null on error
	 */
	public static function getOriginalIP() {
		// If $original_ip is not set, return the REMOTE_ADDR
		if ( ! isset( self::$original_ip ) ) {
			self::$original_ip = $_SERVER['REMOTE_ADDR'];
		}

		return self::$original_ip;
	}

	/**
	 * Checks if the ip is v6.
	 *
	 * @param string $ip IP to check
	 *
	 * @return bool return true if ipv6
	 */
	public static function isIpv6( $ip ) {
		return ! self::isIpv4( $ip );
	}

	/**
	 * Checks if the ip is v4.
	 *
	 * @param string $ip IP to check
	 *
	 * @return bool return true if ipv4
	 */
	public static function isIpv4( $requestIp ) {
		if ( substr_count( $requestIp, ':' ) > 1 ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
	 *
	 * @param string       $requestIp IP to check
	 * @param string|array $ips       List of IPs or subnets (can be a string if only a single one)
	 *
	 * @return bool Whether the IP is valid
	 */
	public static function checkIp( $requestIp, $ips ) {
		if ( ! is_array( $ips ) ) {
			$ips = array( $ips );
		}

		foreach ( $ips as $ip ) {
			if ( self::isIpv4( $requestIp ) && self::checkIp4( $requestIp, $ip ) ||
				( self::isIpv6( $requestIp ) && self::checkIp6( $requestIp, $ip ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Compares two IPv4 addresses.
	 * In case a subnet is given, it checks if it contains the request IP.
	 *
	 * @param string $requestIp IPv4 address to check
	 * @param string $ip        IPv4 address or subnet in CIDR notation
	 *
	 * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet.
	 */
	public static function checkIp4( $requestIp, $ip ) {
		if ( false !== strpos( $ip, '/' ) ) {
			list( $address, $netmask ) = explode( '/', $ip, 2 );

			if ( $netmask === '0' ) {
				// Ensure IP is valid - using ip2long below implicitly validates, but we need to do it manually here
				return filter_var( $address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
			}

			if ( $netmask < 0 || $netmask > 32 ) {
				return false;
			}
		} else {
			$address = $ip;
			$netmask = 32;
		}

		return 0 === substr_compare( sprintf( '%032b', ip2long( $requestIp ) ), sprintf( '%032b', ip2long( $address ) ), 0, $netmask );
	}

	/**
	 * Compares two IPv6 addresses.
	 * In case a subnet is given, it checks if it contains the request IP.
	 *
	 * @see https://github.com/dsp/v6tools
	 *
	 * @param string $requestIp IPv6 address to check
	 * @param string $ip        IPv6 address or subnet in CIDR notation
	 *
	 * @return bool Whether the IP is valid
	 *
	 * @throws \RuntimeException When IPV6 support is not enabled
	 */
	public static function checkIp6( $requestIp, $ip ) {
		if ( ! ( ( extension_loaded( 'sockets' ) && defined( 'AF_INET6' ) ) || @inet_pton( '::1' ) ) ) {
			throw new \RuntimeException( 'Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".' );
		}

		if ( false !== strpos( $ip, '/' ) ) {
			list( $address, $netmask ) = explode( '/', $ip, 2 );

			if ( $netmask < 1 || $netmask > 128 ) {
				return false;
			}
		} else {
			$address = $ip;
			$netmask = 128;
		}

		$bytesAddr = unpack( 'n*', @inet_pton( $address ) );
		$bytesTest = unpack( 'n*', @inet_pton( $requestIp ) );

		if ( ! $bytesAddr || ! $bytesTest ) {
			return false;
		}

		for ( $i = 1, $ceil = ceil( $netmask / 16 ); $i <= $ceil; ++$i ) {
			$left = $netmask - 16 * ( $i - 1 );
			$left = ( $left <= 16 ) ? $left : 16;
			$mask = ~( 0xffff >> $left ) & 0xffff;
			if ( ( $bytesAddr[$i] & $mask ) != ( $bytesTest[ $i ] & $mask ) ) {
				return false;
			}
		}

		return true;
	}

}
