<?php
/**
 * Provides helper functions for the plugin.
 *
 * @package iDevelop_Simple_Custom_WP_Columns
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Defines helper functions for the plugin.
 *
 * @since    1.0.0
 */
class iDevelop_Simple_Custom_WP_Columns_Helpers {

    /**
     * Get all public post types.
     *
     * @since    1.0.0
     * @return   array    An array of public post type objects.
     */
    public static function get_public_post_types() {
        $args = array(
            'public' => true,
        );
        $post_types = get_post_types( $args, 'objects' );
        unset( $post_types['attachment'] ); // Typically don't need columns for attachments.
        return $post_types;
    }

    /**
     * Get all public taxonomies.
     *
     * @since    1.0.0
     * @return   array    An array of public taxonomy objects.
     */
    public static function get_public_taxonomies() {
        $args = array(
            'public' => true,
        );
        $taxonomies = get_taxonomies( $args, 'objects' );
        return $taxonomies;
    }

    /**
     * Get default columns for a given post type.
     *
     * @since    1.0.0
     * @param    string   $post_type    The post type slug.
     * @return   array                  An array of default columns.
     */
    public static function get_default_columns( $post_type ) {
        // Temporarily set screen to get default columns.
        // This is a bit hacky but necessary as get_column_headers() relies on current screen.
        global $wp_list_table;
        $screen = WP_Screen::get( 'edit-' . $post_type );
        $wp_list_table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => $screen ) );
        $columns = $wp_list_table->get_columns();
        return $columns;
    }
}