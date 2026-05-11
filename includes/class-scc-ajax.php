<?php
defined( 'ABSPATH' ) || exit;

class SCC_Ajax {

    public static function init(): void {
        $actions = [
            'scc_get_taxonomies',
            'scc_get_terms',
            'scc_get_acf_fields',
        ];

        foreach ( $actions as $action ) {
            add_action( 'wp_ajax_' . $action, [ __CLASS__, $action ] );
        }
    }

    // Returns taxonomies for a given CPT
    public static function scc_get_taxonomies(): void {
        self::verify_nonce();
        $cpt        = sanitize_text_field( $_POST['cpt'] ?? '' );
        $taxonomies = SCC_Metaboxes::get_taxonomies_for_cpt( $cpt );

        $data = [];
        foreach ( $taxonomies as $tax ) {
            $data[] = [ 'slug' => $tax->name, 'label' => $tax->label ];
        }

        wp_send_json_success( $data );
    }

    // Returns terms for a given taxonomy
    public static function scc_get_terms(): void {
        self::verify_nonce();
        $taxonomy = sanitize_text_field( $_POST['taxonomy'] ?? '' );
        $terms    = SCC_Metaboxes::get_terms_for_taxonomy( $taxonomy );

        $data = [];
        foreach ( $terms as $term ) {
            $data[] = [
                'id'   => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
            ];
        }

        wp_send_json_success( $data );
    }

    // Returns ACF fields for a given CPT
    public static function scc_get_acf_fields(): void {
        self::verify_nonce();
        $cpt    = sanitize_text_field( $_POST['cpt'] ?? '' );
        $fields = SCC_Metaboxes::get_acf_fields_for_cpt( $cpt );
        wp_send_json_success( $fields );
    }

    private static function verify_nonce(): void {
        if (
            ! isset( $_POST['nonce'] ) ||
            ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'scc_admin_nonce' )
        ) {
            wp_send_json_error( [ 'message' => 'Nieprawidłowy nonce.' ], 403 );
        }
    }
}
