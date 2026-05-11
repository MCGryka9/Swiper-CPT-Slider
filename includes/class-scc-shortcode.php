<?php
defined( 'ABSPATH' ) || exit;

class SCC_Shortcode {

    public static function init(): void {
        add_shortcode( 'karuzela', [ __CLASS__, 'render' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
    }

    public static function register_assets(): void {
        // Swiper CSS
        wp_register_style(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            [],
            '11'
        );

        // Swiper JS
        wp_register_script(
            'swiper',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            [],
            '11',
            true
        );

        // Plugin styles
        wp_register_style(
            'scc-public',
            SCC_PLUGIN_URL . 'public/css/carousel.css',
            [ 'swiper' ],
            SCC_VERSION
        );

        // Plugin script
        wp_register_script(
            'scc-public',
            SCC_PLUGIN_URL . 'public/js/carousel.js',
            [ 'swiper' ],
            SCC_VERSION,
            true
        );
    }

    public static function render( array $atts ): string {
        $atts = shortcode_atts( [ 'id' => 0 ], $atts, 'karuzela' );
        $id   = (int) $atts['id'];

        if ( ! $id ) {
            return self::error( __( 'Brak ID karuzeli.', 'swiper-cpt-carousel' ) );
        }

        $carousel = get_post( $id );
        if ( ! $carousel || $carousel->post_type !== 'scc_carousel' || $carousel->post_status !== 'publish' ) {
            return self::error( __( 'Karuzela nie istnieje lub nie jest opublikowana.', 'swiper-cpt-carousel' ) );
        }

        $config = self::get_config( $id );
        $posts  = self::get_posts( $config );

        if ( empty( $posts ) ) {
            return '<p class="scc-no-posts">' . esc_html__( 'Brak postów do wyświetlenia.', 'swiper-cpt-carousel' ) . '</p>';
        }

        // Enqueue assets
        wp_enqueue_style( 'scc-public' );
        wp_enqueue_script( 'scc-public' );

        ob_start();
        include SCC_PLUGIN_DIR . 'templates/carousel.php';
        return ob_get_clean();
    }

    // -------------------------------------------------------------------------

    private static function get_config( int $id ): array {
        return [
            'slides_count'   => (int) ( get_post_meta( $id, '_scc_slides_count', true ) ?: 3 ),
            'cpt'            => get_post_meta( $id, '_scc_cpt', true ) ?: 'post',
            'taxonomy'       => get_post_meta( $id, '_scc_taxonomy', true ) ?: '',
            'terms'          => (array) ( get_post_meta( $id, '_scc_terms', true ) ?: [] ),
            'posts_per_page' => (int) ( get_post_meta( $id, '_scc_posts_per_page', true ) ?: 9 ),
            'acf_fields'     => (array) ( get_post_meta( $id, '_scc_acf_fields', true ) ?: [] ),
            'autoplay'       => (bool) get_post_meta( $id, '_scc_autoplay', true ),
        ];
    }

    private static function get_posts( array $config ): array {
        $args = [
            'post_type'      => $config['cpt'],
            'post_status'    => 'publish',
            'posts_per_page' => $config['posts_per_page'],
            'no_found_rows'  => true,
        ];

        if ( $config['taxonomy'] && ! empty( $config['terms'] ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => $config['taxonomy'],
                    'field'    => 'term_id',
                    'terms'    => $config['terms'],
                ],
            ];
        }

        $query = new WP_Query( $args );
        return $query->posts;
    }

    private static function error( string $msg ): string {
        if ( ! current_user_can( 'manage_options' ) ) {
            return '';
        }
        return '<p class="scc-error" style="color:red;border:1px solid red;padding:8px">' . esc_html( $msg ) . '</p>';
    }
}
