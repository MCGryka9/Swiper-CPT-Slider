<?php
defined( 'ABSPATH' ) || exit;

class SCC_Post_Type {

    public static function init(): void {
        add_action( 'init', [ __CLASS__, 'register_cpt' ] );
    }

    public static function register_cpt(): void {
        $labels = [
            'name'               => __( 'Karuzele', 'swiper-cpt-carousel' ),
            'singular_name'      => __( 'Karuzela', 'swiper-cpt-carousel' ),
            'add_new'            => __( 'Dodaj nową', 'swiper-cpt-carousel' ),
            'add_new_item'       => __( 'Dodaj nową karuzelę', 'swiper-cpt-carousel' ),
            'edit_item'          => __( 'Edytuj karuzelę', 'swiper-cpt-carousel' ),
            'all_items'          => __( 'Wszystkie karuzele', 'swiper-cpt-carousel' ),
            'search_items'       => __( 'Szukaj karuzeli', 'swiper-cpt-carousel' ),
            'menu_name'          => __( 'Karuzele', 'swiper-cpt-carousel' ),
        ];

        register_post_type( 'scc_carousel', [
            'labels'       => $labels,
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-slides',
            'supports'     => [ 'title' ],
            'rewrite'      => false,
        ] );
    }

    public static function activate(): void {
        self::register_cpt();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }
}
