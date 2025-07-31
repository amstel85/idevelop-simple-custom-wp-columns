<?php
/**
 * Handles saving and loading plugin settings, now user-specific.
 *
 * @package iDevelop_Simple_Custom_WP_Columns
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Defines the settings management functionality of the plugin.
 *
 * @since    1.0.0
 */
class iDevelop_Simple_Custom_WP_Columns_Settings {

    /**
     * The meta key for storing user-specific hidden menu settings.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $meta_key    The meta key for user meta.
     */
    private $meta_key;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $meta_key       The meta key to store user settings.
     */
    public function __construct( $meta_key ) {
        $this->meta_key = $meta_key;
    }

    /**
     * Retrieves the hidden menu settings for a specific user.
     *
     * @since    1.0.0
     * @param    int      $user_id    The ID of the user.
     * @return   array                An array of hidden menu slugs for the user.
     */
    public function get_user_hidden_menus( $user_id ) {
        $hidden_menus = get_user_meta( $user_id, $this->meta_key, true );
        return is_array( $hidden_menus ) ? $hidden_menus : array();
    }

    /**
     * Updates the hidden menu settings for a specific user.
     *
     * @since    1.0.0
     * @param    int      $user_id      The ID of the user.
     * @param    array    $hidden_menus An array of menu slugs to hide.
     * @return   bool                   True on success, false on failure.
     */
    public function update_user_hidden_menus( $user_id, $hidden_menus ) {
        // Sanitize the input: ensure it's an array of strings.
        $sanitized_menus = array_map( 'sanitize_text_field', (array) $hidden_menus );
        return update_user_meta( $user_id, $this->meta_key, $sanitized_menus );
    }

    /**
     * Placeholder for settings registration.
     * This class will primarily interact with user meta directly.
     * The settings page will handle form submission and call update_user_hidden_menus.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        // Settings are now handled by direct form submission in templates/settings-page.php
        // and stored via user meta directly, so no register_setting() call is needed here.
    }

    /**
     * Placeholder for sanitization.
     * Sanitization will happen directly in update_user_hidden_menus.
     *
     * @since    1.0.0
     * @param    array    $input    The settings input from the form.
     * @return   array              The sanitized and validated settings.
     */
    public function sanitize_settings( $input ) {
        // This method is no longer used as we are handling user meta directly.
        // However, it's kept as a placeholder if the Settings API still calls it.
        $current_user_id = get_current_user_id();
        $this->update_user_hidden_menus( $current_user_id, $input );
        return $input; // Return the input, as expected by Settings API.
    }
}