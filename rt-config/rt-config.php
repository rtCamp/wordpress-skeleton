<?php
/**
 * Config file
 *
 * This file can be used similarly we are using wp-config.php or rt-config.php file
 * Example: We can for defining constant Keys for the API, Plugins etc. in this file.
 *
 * This file is loaded very early (immediately after `wp-config.php`), which means that most WordPress APIs,
 * classes, and functions are not available. The code below should be limited to pure PHP.
 *
 * @package wordpress-skeleton
 */

// Disable updates for theme and plugin.
define( 'DISALLOW_FILE_MODS', true );

require_once __DIR__ . '/ip-rewrite.php';

// Set IP from Cloudflare to RealIP in site health.
if ( rt_IP_Rewrite::isCloudFlare() ) {
	$_SERVER['REMOTE_ADDR']    = $_SERVER['HTTP_CF_CONNECTING_IP'];
	$_SERVER['HTTP_X_REAL_IP'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
}

/**
 * SES plugin configuration.
 * define( 'AWS_SES_WP_MAIL_REGION', 'us-east-1' );
 * define( 'AWS_SES_WP_MAIL_KEY', 'xxxxxxxxxxxxxxxxxxxx' );
 * define( 'AWS_SES_WP_MAIL_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' );
 */

/**
 * S3-Uploads Plugin configuration.
 * define( 'S3_UPLOADS_BUCKET_URL', 'https://cdn.example.com/wp-content' );
 * define( 'S3_UPLOADS_HTTP_CACHE_CONTROL', 30 * 24 * 60 * 60 );
 * define( 'S3_UPLOADS_BUCKET', 'cdn.example.com/wp-content' );
 * define( 'S3_UPLOADS_KEY', 'xxxxxxxxxxxxxxxxxxxx' );
 * define( 'S3_UPLOADS_SECRET', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' );
 * define( 'S3_UPLOADS_REGION', 'ap-south-1' );
 */

