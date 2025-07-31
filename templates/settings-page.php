<div class="wrap scwc-settings-page">
    <h1><?php esc_html_e( 'Simple Custom WP Columns Settings', 'idevelop-simple-custom-wp-columns' ); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field( 'scwc_save_settings', 'scwc_settings_nonce' ); ?>
        <?php
        // Conditional block to process form submission
        if ( isset( $_POST['submit'] ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'idevelop-simple-custom-wp-columns' ) );
            }

            if ( ! isset( $_POST['scwc_settings_nonce'] ) || ! check_admin_referer( 'scwc_save_settings', 'scwc_settings_nonce' ) ) {
                wp_die( 'Security check failed.' );
            }

            // The old nonce check is no longer needed as we are using the new nonce for the form.
            // check_admin_referer( 'scwc_hidden_menus_update' );

            $current_user_id = get_current_user_id();
            $submitted_menus = isset( $_POST['scwc_hidden_menus'] ) ? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['scwc_hidden_menus'] ) ) : array();

            // Assuming $this->settings is available and is an instance of iDevelop_Simple_Custom_WP_Columns_Settings
            // If not, you might need to instantiate it here:
            // $settings_instance = new iDevelop_Simple_Custom_WP_Columns_Settings( 'idevelop-simple-custom-wp-columns' );
            // $updated = $settings_instance->update_user_hidden_menus( $current_user_id, $submitted_menus );
            $updated = $this->settings->update_user_hidden_menus( $current_user_id, $submitted_menus );

            if ( $updated ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'User-specific hidden menus updated successfully!', 'idevelop-simple-custom-wp-columns' ) . '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Failed to update user-specific hidden menus.', 'idevelop-simple-custom-wp-columns' ) . '</p></div>';
            }
        }
        ?>
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'scwc_hidden_menus_update' ) ); ?>" />

        <?php
        // Display settings sections and fields (still using the API for display)
        do_settings_sections( 'idevelop-simple-custom-wp-columns' ); // Page slug
        ?>

        <label>
            <input type="checkbox" id="scwc-select-all" />
            <?php esc_html_e( 'Select All / Deselect All', 'idevelop-simple-custom-wp-columns' ); ?>
        </label>

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('scwc-select-all');
                const menuCheckboxes = document.querySelectorAll('input[name="scwc_hidden_menus[]"]');

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        menuCheckboxes.forEach(function(checkbox) {
                            checkbox.checked = selectAllCheckbox.checked;
                        });
                    });
                }
            });
        </script>
        <?php
        ?>

        <?php submit_button( __( 'Save Changes', 'idevelop-simple-custom-wp-columns' ), 'primary', 'submit' ); ?>
    </form>
</div>