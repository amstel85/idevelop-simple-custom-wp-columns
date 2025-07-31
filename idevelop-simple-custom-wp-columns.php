<?php
/**
 * Plugin Name: Simple Custom WP Columns
 * Plugin URI:  https://your-website.com/plugins/simple-custom-wp-columns/
 * Description: Provides an intuitive, code-free way to customize WordPress admin list tables by adding or removing columns.
 * Version:     1.0.0
 * Author:      Your Name/Company
 * Author URI:  https://your-website.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: idevelop-simple-custom-wp-columns
 * Domain Path: /languages
 *
 * @package iDevelop_Simple_Custom_WP_Columns
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-scwc-loader.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-scwc-admin.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-scwc-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-scwc-columns.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-scwc-helpers.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-scwc-menu-manager.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file means
 * that all of the plugin's functionality is registered and
 * ready for use.
 *
 * @since    1.0.0
 */
function run_idevelop_simple_custom_wp_columns() {

    $plugin_name = 'idevelop-simple-custom-wp-columns';
    $version = '1.0.0'; // Make sure this matches the plugin header version.

    $loader = new iDevelop_Simple_Custom_WP_Columns_Loader();
    $settings = new iDevelop_Simple_Custom_WP_Columns_Settings( $plugin_name );
    $menu_manager = new iDevelop_Simple_Custom_WP_Columns_Menu_Manager( $settings );
    $admin = new iDevelop_Simple_Custom_WP_Columns_Admin( $plugin_name, $version, $settings, $menu_manager );
    $columns = new iDevelop_Simple_Custom_WP_Columns_Columns( $settings );


    $loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
    $loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
    $loader->add_action( 'admin_menu', $admin, 'add_plugin_admin_menu' );
    $loader->add_action( 'admin_init', $settings, 'register_settings' );
    $loader->add_action( 'admin_menu', $menu_manager, 'filter_admin_menu', 99 );

    // Dynamic hooks for columns.
    $post_types = iDevelop_Simple_Custom_WP_Columns_Helpers::get_public_post_types();
    foreach ( $post_types as $post_type_obj ) {
        $post_type_slug = $post_type_obj->name;
        $loader->add_filter( 'manage_' . $post_type_slug . '_posts_columns', $columns, 'manage_posts_columns', 10, 2 );
        $loader->add_action( 'manage_' . $post_type_slug . '_posts_custom_column', $columns, 'manage_posts_custom_column', 10, 2 );
        $loader->add_filter( 'manage_edit-' . $post_type_slug . '_sortable_columns', $columns, 'register_sortable_columns' );
    }
    $loader->add_action( 'pre_get_posts', $columns, 'custom_column_orderby' );

    $loader->run();

}
run_idevelop_simple_custom_wp_columns();