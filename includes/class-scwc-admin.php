<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package iDevelop_Simple_Custom_WP_Columns
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Defines the admin-specific functionality of the plugin.
 *
 * @since    1.0.0
 */
class iDevelop_Simple_Custom_WP_Columns_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

   /**
    * The instance of the settings class.
    *
    * @since    1.0.0
    * @access   private
    * @var      iDevelop_Simple_Custom_WP_Columns_Settings    $settings    The settings instance.
    */
   private $settings;

   /**
    * The instance of the menu manager class.
    *
    * @since    1.0.0
    * @access   private
    * @var      iDevelop_Simple_Custom_WP_Columns_Menu_Manager    $menu_manager    The menu manager instance.
    */
   private $menu_manager;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     * @param      iDevelop_Simple_Custom_WP_Columns_Settings    $settings          The settings instance.
     * @param      iDevelop_Simple_Custom_WP_Columns_Menu_Manager    $menu_manager      The menu manager instance.
     */
    public function __construct( $plugin_name, $version, $settings, iDevelop_Simple_Custom_WP_Columns_Menu_Manager $menu_manager ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings = $settings;
        $this->menu_manager = $menu_manager;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../admin/css/admin-style.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../admin/js/admin-script.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Add the top-level menu page for the plugin.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {

        add_menu_page(
            'Simple Custom WP Columns Settings',
            'WP Columns',
            'manage_options',
            'idevelop-simple-custom-wp-columns',
            array( $this, 'display_plugin_setup_page' ),
            'dashicons-admin-generic',
            99
        );

    }

    /**
     * Render the settings page for the plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        // Settings sections and fields are now handled directly in the template for display.
        // The saving mechanism is also handled directly in the template.

        // Add settings section and field for hidden menus (for display only)
        add_settings_section(
            'scwc_hidden_menus_section',
            __( 'Hidden Menus', 'idevelop-simple-custom-wp-columns' ),
            null,
            $this->plugin_name
        );

        add_settings_field(
            'scwc_hidden_menus_field',
            __( 'Select Menus to Hide', 'idevelop-simple-custom-wp-columns' ),
            array( $this, 'display_hidden_menus_field_callback' ),
            $this->plugin_name,
            'scwc_hidden_menus_section'
        );

        include_once plugin_dir_path( __FILE__ ) . '../templates/settings-page.php';
    }

    /**
     * Register all of the settings sections and fields for the plugin.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        // Settings will be registered here by class-scwc-settings.php
    }

    /**
     * Render the hidden menus field.
     *
     * @since    1.0.0
     */
    public function display_hidden_menus_field_callback() {
        $current_user_id = get_current_user_id();
        $hidden_menus = $this->settings->get_user_hidden_menus( $current_user_id );
        $all_admin_menus = $this->menu_manager->get_all_admin_menus();

        echo '<p>' . esc_html__( 'Select the menus you want to hide for your user.', 'idevelop-simple-custom-wp-columns' ) . '</p>';
        echo '<div class="scwc-menu-checkboxes">';
        foreach ( $all_admin_menus as $menu_slug => $menu_title ) {
            $checked = in_array( $menu_slug, $hidden_menus, true ) ? 'checked' : '';
            echo '<label>';
            echo '<input type="checkbox" name="scwc_hidden_menus[]" value="' . esc_attr( $menu_slug ) . '" ' . checked( true, in_array( $menu_slug, $hidden_menus, true ), false ) . '>';
            echo esc_html( $menu_title );
            echo '</label><br>';
        }
        echo '</div>';
    }

}