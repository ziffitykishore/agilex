<?php
/*
Plugin Name: Love It
Plugin URI: http://pippinsplugins.com
Description: Adds a "Love It" link to posts, pages, and custom post types
Version: 1.0.5
Author: Pippin Williamson
Contributors: mordauk
Author URI: http://pippinsplugins.com
*/

/***************************
* constants
***************************/

if(!defined('LI_BASE_DIR')) {
	define('LI_BASE_DIR', dirname(__FILE__));
}
if(!defined('LI_BASE_URL')) {
	define('LI_BASE_URL', plugin_dir_url(__FILE__));
}


/***************************
* language files
***************************/
function li_load_text_domain() {
	load_plugin_textdomain( 'love_it', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'li_load_text_domain' );

/***************************
* includes
***************************/
include(LI_BASE_DIR . '/includes/display-functions.php');
include(LI_BASE_DIR . '/includes/love-functions.php');
include(LI_BASE_DIR . '/includes/scripts.php');
include(LI_BASE_DIR . '/includes/widgets.php');
