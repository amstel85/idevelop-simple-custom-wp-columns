<?php
/**
 * Handles the custom column logic for admin list tables.
 *
 * @package iDevelop_Simple_Custom_WP_Columns
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Defines the column management functionality of the plugin.
 *
 * @since    1.0.0
 */
class iDevelop_Simple_Custom_WP_Columns_Columns {

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
     * Filters the columns for a specific post type.
     *
     * @since    1.0.0
     * @param    array    $columns      An array of column names.
     * @param    string   $post_type    The post type.
     * @return   array                  Modified array of column names.
     */
    public function manage_posts_columns( $columns, $post_type ) {
        $plugin_settings = $this->settings->get_settings();

        if ( isset( $plugin_settings[ $post_type ] ) ) {
            $post_type_settings = $plugin_settings[ $post_type ];

            // Remove columns.
            if ( isset( $post_type_settings['hidden_columns'] ) && is_array( $post_type_settings['hidden_columns'] ) ) {
                foreach ( $post_type_settings['hidden_columns'] as $column_key ) {
                    unset( $columns[ $column_key ] );
                }
            }

            // Add new columns.
            if ( isset( $post_type_settings['custom_columns'] ) && is_array( $post_type_settings['custom_columns'] ) ) {
                $new_columns = array();
                foreach ( $post_type_settings['custom_columns'] as $column_id => $column_data ) {
                    $new_columns[ $column_id ] = $column_data['header'];
                }
                $columns = array_merge( $columns, $new_columns );
            }
        }

        return $columns;
    }

    /**
     * Displays the content for custom columns.
     *
     * @since    1.0.0
     * @param    string   $column_name    The name of the column to display.
     * @param    int      $post_id        The current post ID.
     */
    public function manage_posts_custom_column( $column_name, $post_id ) {
        $plugin_settings = $this->settings->get_settings();
        $post_type       = get_post_type( $post_id );

        if ( isset( $plugin_settings[ $post_type ]['custom_columns'][ $column_name ] ) ) {
            $column_data = $plugin_settings[ $post_type ]['custom_columns'][ $column_name ];

            switch ( $column_data['data_source_type'] ) {
                case 'post_meta':
                    echo esc_html( get_post_meta( $post_id, $column_data['data_source_key'], true ) );
                    break;
                case 'taxonomy':
                    $terms = get_the_terms( $post_id, $column_data['data_source_key'] );
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                        $output = array();
                        foreach ( $terms as $term ) {
                            $output[] = esc_html( $term->name );
                        }
                        echo esc_html( implode( ', ', $output ) );
                    }
                    break;
                // Add other data sources here as needed.
            }
        }
    }

    /**
     * Makes custom columns sortable.
     *
     * @since    1.0.0
     * @param    array    $columns    The array of sortable columns.
     * @return   array                Modified array of sortable columns.
     */
    public function register_sortable_columns( $columns ) {
        $plugin_settings = $this->settings->get_settings();
        $screen          = get_current_screen();

        if ( $screen && isset( $plugin_settings[ $screen->post_type ]['custom_columns'] ) ) {
            foreach ( $plugin_settings[ $screen->post_type ]['custom_columns'] as $column_id => $column_data ) {
                if ( isset( $column_data['sortable'] ) && $column_data['sortable'] ) {
                    $columns[ $column_id ] = $column_data['data_source_key']; // Use meta key or taxonomy slug for sorting.
                }
            }
        }
        return $columns;
    }

    /**
     * Handles custom column sorting queries.
     *
     * @since    1.0.0
     * @param    WP_Query    $query    The WP_Query instance.
     */
    public function custom_column_orderby( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        $orderby = $query->get( 'orderby' );
        $plugin_settings = $this->settings->get_settings();
        $screen          = get_current_screen();

        if ( $screen && isset( $plugin_settings[ $screen->post_type ]['custom_columns'] ) ) {
            foreach ( $plugin_settings[ $screen->post_type ]['custom_columns'] as $column_id => $column_data ) {
                if ( isset( $column_data['sortable'] ) && $column_data['sortable'] && $orderby === $column_data['data_source_key'] ) {
                    switch ( $column_data['data_source_type'] ) {
                        case 'post_meta':
                            $query->set( 'meta_key', $column_data['data_source_key'] );
                            $query->set( 'orderby', 'meta_value' );
                            break;
                        case 'taxonomy':
                            $query->set( 'taxonomy', $column_data['data_source_key'] );
                            $query->set( 'orderby', 'term_id' ); // Or 'name', 'slug', etc.
                            break;
                    }
                }
            }
        }
    }
}