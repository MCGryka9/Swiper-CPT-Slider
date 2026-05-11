<?php
defined( 'ABSPATH' ) || exit;

class SCC_Admin {

    public static function init(): void {
        add_filter( 'manage_scc_carousel_posts_columns',       [ __CLASS__, 'columns' ] );
        add_action( 'manage_scc_carousel_posts_custom_column', [ __CLASS__, 'column_content' ], 10, 2 );
        add_filter( 'post_row_actions',                        [ __CLASS__, 'row_actions' ], 10, 2 );
    }

    public static function columns( array $columns ): array {
        $new = [];
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( $key === 'title' ) {
                $new['scc_shortcode'] = __( 'Shortcode', 'swiper-cpt-carousel' );
                $new['scc_cpt']       = __( 'Typ posta', 'swiper-cpt-carousel' );
                $new['scc_slides']    = __( 'Slajdy', 'swiper-cpt-carousel' );
            }
        }
        return $new;
    }

    public static function column_content( string $column, int $post_id ): void {
        switch ( $column ) {
            case 'scc_shortcode':
                $code = '[karuzela id="' . $post_id . '"]';
                echo '<code onclick="navigator.clipboard.writeText(\'' . esc_js( $code ) . '\');this.style.background=\'#d4edda\'" title="Kliknij, aby skopiować" style="cursor:pointer">' . esc_html( $code ) . '</code>';
                break;

            case 'scc_cpt':
                $cpt = get_post_meta( $post_id, '_scc_cpt', true );
                $obj = $cpt ? get_post_type_object( $cpt ) : null;
                echo esc_html( $obj ? $obj->label : ( $cpt ?: '—' ) );
                break;

            case 'scc_slides':
                $count = get_post_meta( $post_id, '_scc_slides_count', true );
                echo esc_html( $count ?: '—' );
                break;
        }
    }

    public static function row_actions( array $actions, WP_Post $post ): array {
        if ( $post->post_type !== 'scc_carousel' ) {
            return $actions;
        }
        // Nothing extra for now – clean
        return $actions;
    }
}
