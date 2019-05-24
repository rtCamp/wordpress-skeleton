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
