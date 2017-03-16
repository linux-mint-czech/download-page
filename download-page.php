<?php
/*
Plugin Name:    Linux Mint download page
Plugin URI:     https://github.com/linux-mint-czech/download-page
Version:        1.1
Author:         Linux-Mint-Czech.cz
Author URI:     https://www.linux-mint-czech.cz/
License:        GNU General Public License v2.0
License URI:    http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die();
define( 'DOWNLOAD_PAGE_PLUGIN_FILE', __FILE__ );
define( 'DOWNLOAD_PAGE_PLUGIN_DIR', trailingslashit( plugin_dir_path( DOWNLOAD_PAGE_PLUGIN_FILE ) ) );

spl_autoload_register( 'download_page_autoload' );

/**
 * Handles plugin classes autoloading (all should be prefixed by 'Download_Page_').
 * 
 * @param string $class_name
 * @return true if the class has been loaded successfully
 */
function download_page_autoload( $class_name ) {
	if ( substr( $class_name, 0, strlen('Download_Page_') ) === 'Download_Page_' ) {
		$class_path = DOWNLOAD_PAGE_PLUGIN_DIR . 'classes/' . str_replace( "\\", '/', $class_name ) . '.php';
		if ( file_exists( $class_path ) ) {
			require $class_path;
			return true;
		}
	}
	return false;
}

add_shortcode( 'download-page', array('Download_Page_Shortcode', 'do_shortcode') );

