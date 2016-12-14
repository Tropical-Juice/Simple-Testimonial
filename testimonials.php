<?php
/*
	Plugin Name: Simple Google Testimonials
	Plugin URI: https://tropicaljuice.nl/
	Description: Users can add testimonials on front-end of website. You can manage these as 'regular' posts at the wp-admin. 
	Version: 1.0
	Author: Tropical Juice
	Author URI: https://tropicaljuice.nl/
	Text Domain: tropical_testimonials
	License: GPLv3
	License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; 
define( 'TROPICAL_TESTIMONIALS_VERSION', '1.0' );
define( 'TROPICAL_TESTIMONIALS_MIN_WP_VERSION', '3.7' );
define( 'TROPICAL_TESTIMONIALS_TEXT_DOMAIN', 'tropical_testimonials' );
define( 'TROPICAL_TESTIMONIALS_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'TROPICAL_TESTIMONIALS_PLUGIN_DIR', basename(dirname(__FILE__)) );
define( 'TROPICAL_TESTIMONIALS_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'TROPICAL_TESTIMONIALS_PLUGIN_FILE_NAME', plugin_basename(__FILE__) );
require_once( TROPICAL_TESTIMONIALS_PLUGIN_DIR_PATH . 'testimonials.class.php' );
add_action( 'init', array( 'Testimonials', 'registerPostTypes') );
add_action( 'init', array( 'Testimonials', 'init' ) );
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	//admin options here
}