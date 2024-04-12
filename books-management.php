<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/parthsanghvi/
 * @since             1.0.0
 * @package           Books_Management
 *
 * @wordpress-plugin
 * Plugin Name:       Books Management
 * Plugin URI:        https://github.com/ParthSanghvi
 * Description:       Plugin will manage books with authors and publications
 * Version:           1.0.0
 * Author:            Parth
 * Author URI:        https://profiles.wordpress.org/parthsanghvi//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       books-management
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOOKS_MANAGEMENT_VERSION', '1.0.0' );

//Plugin URL
if ( ! defined( 'BM_PLUGIN_URL' ) ) {
    define( 'BM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-books-management-activator.php
 */
function activate_books_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-books-management-activator.php';
	Books_Management_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-books-management-deactivator.php
 */
function deactivate_books_management() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-books-management-deactivator.php';
	Books_Management_Deactivator::deactivate();
}

/*
    Create a page with Book Library on plugin activation and add shortcode into the post content.
*/
function bm_create_plugin_page_and_add_shortcode() {
    $page_id = wp_insert_post( array(
        'post_title'    => 'Book Library',
        'post_content'  => '[book_grid]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
    ) );

    if ( $page_id && ! is_wp_error( $page_id ) ) {
        update_option( 'plugin_page_id', $page_id );
    }
}

/*
    Delete page with Book Libbbrary on plugin deactivation.
*/
function bm_delete_plugin_page_and_shortcode() {
    $page_id = get_option( 'plugin_page_id' );

    if ( $page_id ) {
        wp_delete_post( $page_id, true );
        delete_option( 'plugin_page_id' );
    }
}

register_activation_hook( __FILE__, 'activate_books_management' );
register_deactivation_hook( __FILE__, 'deactivate_books_management' );
register_activation_hook( __FILE__, 'bm_create_plugin_page_and_add_shortcode' );
register_deactivation_hook( __FILE__, 'bm_delete_plugin_page_and_shortcode' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-books-management.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_books_management() {

	$plugin = new Books_Management();
	$plugin->run();

}
run_books_management();
