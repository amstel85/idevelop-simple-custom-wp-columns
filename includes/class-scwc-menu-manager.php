<?php
/**
 * Manages the visibility of admin menu items.
 *
 * @package iDevelop_Simple_Custom_WP_Columns
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Defines the menu management functionality of the plugin.
 *
 * @since    1.0.0
 */
class iDevelop_Simple_Custom_WP_Columns_Menu_Manager {

    /**
     * The settings instance.
     *
     * @since    1.0.0
     * @access   private
     * @var      iDevelop_Simple_Custom_WP_Columns_Settings    $settings    The settings instance.
     */
    private $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      iDevelop_Simple_Custom_WP_Columns_Settings    $settings    The settings instance.
     */
    public function __construct( $settings ) {
        $this->settings = $settings;
    }

    /**
     * Filters the admin menu to hide items based on user settings.
     *
     * @since    1.0.0
     */
    public function filter_admin_menu() {
        // Verify nonce for admin form submissions.
        // The nonce action is 'scwc_save_settings' as used in templates/settings-page.php.
        if ( isset( $_POST['submit'] ) ) {
            check_admin_referer( 'scwc_save_settings', 'scwc_settings_nonce' );
        }

        if ( ! is_admin() ) {
            return;
        }

        // Prevent menu filtering on the plugin's own settings page.
        // The plugin slug is 'idevelop-simple-custom-wp-columns'.
        if ( isset( $_GET['page'] ) && 'idevelop-simple-custom-wp-columns' === $_GET['page'] ) {
            return;
        }

        global $menu, $submenu;

        $current_user_id = get_current_user_id();
        $user_hidden_menus = $this->settings->get_user_hidden_menus( $current_user_id );

        if ( empty( $user_hidden_menus ) ) {
            return;
        }

        // Hide top-level menu items.
        foreach ( $menu as $key => $menu_item ) {
            $menu_slug = isset( $menu_item[2] ) ? $menu_item[2] : '';
            // Ensure the plugin's own settings page is never hidden.
            if ( 'idevelop-simple-custom-wp-columns' === $menu_slug ) {
                continue;
            }
            if ( in_array( $menu_slug, $user_hidden_menus ) ) {
                unset( $menu[ $key ] );
            }
        }

        // Hide sub-menu items.
        foreach ( $submenu as $parent_slug => $sub_menu_items ) {
            foreach ( $sub_menu_items as $key => $sub_menu_item ) {
                $sub_menu_slug = isset( $sub_menu_item[2] ) ? $sub_menu_item[2] : '';
                // Ensure the plugin's own settings page is never hidden.
                if ( 'idevelop-simple-custom-wp-columns' === $sub_menu_slug ) {
                    continue;
                }
                if ( in_array( $sub_menu_slug, $user_hidden_menus ) ) {
                    unset( $submenu[ $parent_slug ][ $key ] );
                }
            }
        }
    }

    /**
     * Retrieves all top-level and sub-level admin menu items.
     *
     * @since    1.0.0
     * @return   array    An associative array of menu slugs and their titles.
     */
    public function get_all_admin_menus() {
        if ( ! is_admin() ) {
            return array();
        }

        global $menu;
        $all_menus = array();

        // Process top-level menus.
        foreach ( $menu as $menu_item ) {
            $menu_slug = isset( $menu_item[2] ) ? $menu_item[2] : '';
            $menu_title = isset( $menu_item[0] ) ? wp_strip_all_tags( $menu_item[0] ) : '';
            // Remove numbers in parentheses, standalone numbers, and HTML spans.
            // The HTML spans are already removed by wp_strip_all_tags, but keeping the regex for robustness.
            $menu_title = preg_replace( '/\s*\(.*?\)\s*|^\s*\d+\s*|\s*\d+\s*$|\s*' . __( 'בהמתנה', 'idevelop-simple-custom-wp-columns' ) . '\s*/u', '', $menu_title );
            $menu_title = trim( $menu_title ); // Trim any remaining whitespace.

            // Exclude the plugin's own settings page from the list of selectable menus.
            if ( 'idevelop-simple-custom-wp-columns' === $menu_slug ) {
                continue;
            }

            if ( ! empty( $menu_slug ) && ! empty( $menu_title ) ) {
                $all_menus[ $menu_slug ] = $menu_title;
            }
        }

        // Sort menus alphabetically by title for better display.
        asort( $all_menus );

        return $all_menus;
    }
}