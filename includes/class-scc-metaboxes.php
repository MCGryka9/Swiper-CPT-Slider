<?php
defined( 'ABSPATH' ) || exit;

class SCC_Metaboxes {

    public static function init(): void {
        add_action( 'add_meta_boxes', [ __CLASS__, 'register' ] );
        add_action( 'save_post_scc_carousel', [ __CLASS__, 'save' ], 10, 2 );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    public static function enqueue_assets( string $hook ): void {
        $screen = get_current_screen();
        if ( ! $screen || $screen->post_type !== 'scc_carousel' ) {
            return;
        }

        wp_enqueue_style(
            'scc-admin-style',
            SCC_PLUGIN_URL . 'admin/admin.css',
            [],
            SCC_VERSION
        );

        wp_enqueue_script(
            'scc-admin-script',
            SCC_PLUGIN_URL . 'admin/admin.js',
            [ 'jquery' ],
            SCC_VERSION,
            true
        );

        wp_localize_script( 'scc-admin-script', 'SCC_Admin', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'scc_admin_nonce' ),
        ] );
    }

    public static function register(): void {
        add_meta_box(
            'scc_carousel_settings',
            __( 'Ustawienia karuzeli', 'swiper-cpt-carousel' ),
            [ __CLASS__, 'render_settings' ],
            'scc_carousel',
            'normal',
            'high'
        );

        add_meta_box(
            'scc_carousel_shortcode',
            __( 'Shortcode', 'swiper-cpt-carousel' ),
            [ __CLASS__, 'render_shortcode' ],
            'scc_carousel',
            'side',
            'high'
        );
    }

    public static function render_shortcode( WP_Post $post ): void {
        if ( $post->post_status === 'auto-draft' ) {
            echo '<p>' . esc_html__( 'Zapisz karuzelę, aby wygenerować shortcode.', 'swiper-cpt-carousel' ) . '</p>';
            return;
        }
        $code = '[karuzela id="' . $post->ID . '"]';
        echo '<div class="scc-shortcode-box">';
        echo '<input type="text" readonly value="' . esc_attr( $code ) . '" class="widefat" onclick="this.select()">';
        echo '<p class="description">' . esc_html__( 'Kliknij, aby zaznaczyć i skopiuj shortcode.', 'swiper-cpt-carousel' ) . '</p>';
        echo '</div>';
    }

    public static function render_settings( WP_Post $post ): void {
        wp_nonce_field( 'scc_save_carousel', 'scc_nonce' );

        $slides_count  = get_post_meta( $post->ID, '_scc_slides_count', true ) ?: '3';
        $cpt           = get_post_meta( $post->ID, '_scc_cpt', true ) ?: '';
        $taxonomy      = get_post_meta( $post->ID, '_scc_taxonomy', true ) ?: '';
        $terms_sel     = get_post_meta( $post->ID, '_scc_terms', true ) ?: [];
        $acf_fields    = get_post_meta( $post->ID, '_scc_acf_fields', true ) ?: [];
        $posts_per_page = get_post_meta( $post->ID, '_scc_posts_per_page', true ) ?: '9';
        $autoplay      = get_post_meta( $post->ID, '_scc_autoplay', true ) ?: '0';

        $all_cpts = self::get_public_cpts();

        include SCC_PLUGIN_DIR . 'templates/metabox-settings.php';
    }

    public static function save( int $post_id, WP_Post $post ): void {
        if (
            ! isset( $_POST['scc_nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['scc_nonce'] ) ), 'scc_save_carousel' ) ||
            defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
            ! current_user_can( 'edit_post', $post_id )
        ) {
            return;
        }

        $fields = [
            '_scc_slides_count'   => 'intval',
            '_scc_cpt'            => 'sanitize_text_field',
            '_scc_taxonomy'       => 'sanitize_text_field',
            '_scc_posts_per_page' => 'intval',
            '_scc_autoplay'       => 'intval',
        ];

        foreach ( $fields as $key => $sanitize ) {
            $raw = isset( $_POST[ ltrim( $key, '_' ) ] )
                ? wp_unslash( $_POST[ ltrim( $key, '_' ) ] )
                : ( isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '' );

            // Try both with and without leading underscore prefix
            $post_key = ltrim( $key, '_' );
            $value    = isset( $_POST[ $post_key ] ) ? $sanitize( wp_unslash( $_POST[ $post_key ] ) ) : '';
            update_post_meta( $post_id, $key, $value );
        }

        // Terms (array of ints)
        $terms = isset( $_POST['scc_terms'] ) ? array_map( 'intval', (array) $_POST['scc_terms'] ) : [];
        update_post_meta( $post_id, '_scc_terms', $terms );

        // ACF fields (array of sanitized strings)
        $acf_raw = isset( $_POST['scc_acf_fields'] ) ? (array) $_POST['scc_acf_fields'] : [];
        $acf_fields = [];
        foreach ( $acf_raw as $row ) {
            $key_val   = sanitize_key( $row['key'] ?? '' );
            $label_val = sanitize_text_field( $row['label'] ?? '' );
            if ( $key_val ) {
                $acf_fields[] = [ 'key' => $key_val, 'label' => $label_val ];
            }
        }
        update_post_meta( $post_id, '_scc_acf_fields', $acf_fields );
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public static function get_public_cpts(): array {
        $args = [
            'public'   => true,
            '_builtin' => false,
        ];
        $cpts   = get_post_types( $args, 'objects' );
        // Add built-in post & page
        $result = [
            'post' => get_post_type_object( 'post' ),
            'page' => get_post_type_object( 'page' ),
        ];
        return array_merge( $result, $cpts );
    }

    public static function get_taxonomies_for_cpt( string $cpt ): array {
        if ( ! $cpt ) {
            return [];
        }
        $taxonomies = get_object_taxonomies( $cpt, 'objects' );
        return array_filter( $taxonomies, fn( $t ) => $t->show_ui );
    }

    public static function get_terms_for_taxonomy( string $taxonomy ): array {
        if ( ! $taxonomy ) {
            return [];
        }
        $terms = get_terms( [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ] );
        return is_wp_error( $terms ) ? [] : $terms;
    }

    public static function get_acf_fields_for_cpt( string $cpt ): array {
        if ( ! function_exists( 'acf_get_field_groups' ) || ! $cpt ) {
            return [];
        }

        $groups = acf_get_field_groups( [ 'post_type' => $cpt ] );
        $fields = [];

        foreach ( $groups as $group ) {
            $group_fields = acf_get_fields( $group['key'] );
            if ( ! $group_fields ) {
                continue;
            }
            foreach ( $group_fields as $field ) {
                // Only simple field types make sense as text on a slide
                $text_types = [ 'text', 'number', 'email', 'url', 'date_picker', 'date_time_picker', 'select', 'radio', 'true_false' ];
                if ( in_array( $field['type'], $text_types, true ) ) {
                    $fields[] = [
                        'key'   => $field['name'],
                        'label' => $field['label'],
                    ];
                }
            }
        }

        return $fields;
    }
}
