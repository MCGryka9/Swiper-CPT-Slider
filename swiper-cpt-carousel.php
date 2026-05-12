<?php
/**
 * Plugin Name: Swiper CPT Carousel
 * Plugin URI:  https://example.com
 * Description: Konfigurowalne karuzele Swiper.js dla dowolnych Custom Post Types z obsługą ACF. Użycie: [karuzela id="1"]
 * Version:     1.0.0
 * Author:      Your Name
 * Text Domain: swiper-cpt-carousel
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

defined( 'ABSPATH' ) || exit;

define( 'SCC_VERSION',     '1.0.0' );
define( 'SCC_PLUGIN_FILE', __FILE__ );
define( 'SCC_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'SCC_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

require_once SCC_PLUGIN_DIR . 'includes/class-scc-post-type.php';
require_once SCC_PLUGIN_DIR . 'includes/class-scc-metaboxes.php';
require_once SCC_PLUGIN_DIR . 'includes/class-scc-shortcode.php';
require_once SCC_PLUGIN_DIR . 'includes/class-scc-ajax.php';
require_once SCC_PLUGIN_DIR . 'admin/class-scc-admin.php';

register_activation_hook(   __FILE__, [ 'SCC_Post_Type', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'SCC_Post_Type', 'deactivate' ] );

add_action( 'plugins_loaded', function () {
    SCC_Post_Type::init();
    SCC_Metaboxes::init();
    SCC_Shortcode::init();
    SCC_Ajax::init();
    SCC_Admin::init();
} );

// Licznik wyświetleń wpisów (do sortowania karuzeli po popularności)
add_action( 'wp_head', function () {
    if ( is_singular() ) {
        $id    = get_the_ID();
        $count = (int) get_post_meta( $id, 'post_views_count', true );
        update_post_meta( $id, 'post_views_count', $count + 1 );
    }
} );