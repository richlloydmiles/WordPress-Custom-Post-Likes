<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://richymiles.wordpress.com
 * @since      1.0.1
 *
 * @package    Simple_custom_post_likes
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

$option_names = array(
	'favourates_post_type', 
	'favourates_element'
);

if ( is_multisite() ) {
	$ms_sites = wp_get_sites();
	if( 0 < sizeof( $ms_sites ) ) {
		foreach ( $ms_sites as $ms_site ) {
			switch_to_blog( $ms_site['blog_id'] );
			if( sizeof( $option_names ) > 0 ) {
				foreach( $option_names as $option_name ) {
					delete_option( $option_name );
					plugin_uninstalled();
				}
			}
		}
	}
	restore_current_blog();
} else {
	if( sizeof( $option_names ) > 0 ) {
		foreach( $option_names as $option_name ) {
			delete_option( $option_name );
			plugin_uninstalled();
		}
	}
}

/**
 * Delete plugin meta when uninstalled
 *
 * @access public
 * @return void
 */
function plugin_uninstalled() {
	global $wpdb;
	$post_meta_names = array( 'favourite_users');
	if( sizeof( $post_meta_names ) > 0 ) {
		foreach( $post_meta_names as $post_meta_name ) {
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key = '$post_meta_name'" );
		}
	}
}