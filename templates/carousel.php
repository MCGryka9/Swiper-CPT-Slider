<?php
/**
 * Template: carousel.php
 *
 * @var WP_Post[] $posts
 * @var array     $config
 * @var int       $id     (carousel post ID)
 */
defined( 'ABSPATH' ) || exit;

$uid         = 'scc-' . $id . '-' . wp_unique_id();
$slides_vis  = $config['slides_count'];
$acf_fields  = $config['acf_fields'];
$autoplay    = $config['autoplay'];

// Swiper breakpoints based on slides_count
$bp = match ( (int) $slides_vis ) {
    1 => [ 'slidesPerView' => 1, 'bp' => [] ],
    2 => [ 'slidesPerView' => 1, 'bp' => [ 640 => 2 ] ],
    default => [ 'slidesPerView' => 1, 'bp' => [ 640 => 2, 1024 => 3 ] ],
};
?>

<div class="scc-carousel-wrap" id="<?php echo esc_attr( $uid . '-wrap' ); ?>">
    <div class="swiper <?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'Karuzela postów', 'swiper-cpt-carousel' ); ?>">
        <div class="swiper-wrapper">
            <?php foreach ( $posts as $post ) :
                setup_postdata( $post );
                $thumb_id  = get_post_thumbnail_id( $post->ID );
                $thumb_url = $thumb_id
                    ? wp_get_attachment_image_url( $thumb_id, 'large' )
                    : SCC_PLUGIN_URL . 'public/img/placeholder.svg';
                $thumb_alt = $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : '';
                $thumb_alt = $thumb_alt ?: get_the_title( $post->ID );
                $post_url  = get_permalink( $post->ID );
                $excerpt   = has_excerpt( $post->ID )
                    ? get_the_excerpt( $post->ID )
                    : wp_trim_words( get_the_content( null, false, $post->ID ), 20 );
            ?>
            <div class="swiper-slide">
                <a class="scc-slide" href="<?php echo esc_url( $post_url ); ?>" aria-label="<?php echo esc_attr( get_the_title( $post->ID ) ); ?>">

                    <!-- Thumbnail -->
                    <div class="scc-slide__img-wrap">
                        <img
                            class="scc-slide__img"
                            src="<?php echo esc_url( $thumb_url ); ?>"
                            alt="<?php echo esc_attr( $thumb_alt ); ?>"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>

                    <!-- Content -->
                    <div class="scc-slide__body">
                        <h3 class="scc-slide__title"><?php echo esc_html( get_the_title( $post->ID ) ); ?></h3>

                        <?php if ( $excerpt ) : ?>
                            <p class="scc-slide__excerpt"><?php echo esc_html( $excerpt ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $acf_fields ) && function_exists( 'get_field' ) ) : ?>
                            <ul class="scc-slide__acf">
                                <?php foreach ( $acf_fields as $field ) :
                                    $val = get_field( $field['key'], $post->ID );
                                    if ( $val === null || $val === '' ) continue;
                                    if ( is_array( $val ) ) $val = implode( ', ', $val );
                                ?>
                                    <li class="scc-slide__acf-item">
                                        <?php if ( ! empty( $field['label'] ) ) : ?>
                                            <span class="scc-slide__acf-label"><?php echo esc_html( $field['label'] ); ?>:</span>
                                        <?php endif; ?>
                                        <span class="scc-slide__acf-value"><?php echo esc_html( (string) $val ); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <span class="scc-slide__btn" aria-hidden="true">
                            <?php esc_html_e( 'Czytaj więcej', 'swiper-cpt-carousel' ); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </span>
                    </div><!-- .scc-slide__body -->

                </a><!-- .scc-slide -->
            </div><!-- .swiper-slide -->
            <?php endforeach; wp_reset_postdata(); ?>
        </div><!-- .swiper-wrapper -->

        <!-- Navigation -->
        <div class="swiper-button-prev scc-nav-prev" aria-label="<?php esc_attr_e( 'Poprzedni', 'swiper-cpt-carousel' ); ?>"></div>
        <div class="swiper-button-next scc-nav-next" aria-label="<?php esc_attr_e( 'Następny', 'swiper-cpt-carousel' ); ?>"></div>

        <!-- Pagination -->
        <div class="swiper-pagination scc-pagination"></div>
    </div><!-- .swiper -->
</div><!-- .scc-carousel-wrap -->

<script>
(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var bpObj = {};
        <?php foreach ( $bp['bp'] as $minWidth => $perView ) : ?>
        bpObj[<?php echo (int) $minWidth; ?>] = { slidesPerView: <?php echo (int) $perView; ?>, spaceBetween: 24 };
        <?php endforeach; ?>

        new Swiper('.<?php echo esc_js( $uid ); ?>', {
            slidesPerView: <?php echo (int) $bp['slidesPerView']; ?>,
            spaceBetween: 24,
            loop: true,
            breakpoints: bpObj,
            navigation: {
                nextEl: '.<?php echo esc_js( $uid ); ?> .scc-nav-next',
                prevEl: '.<?php echo esc_js( $uid ); ?> .scc-nav-prev',
            },
            pagination: {
                el: '.<?php echo esc_js( $uid ); ?> .scc-pagination',
                clickable: true,
            },
            a11y: { enabled: true },
            <?php if ( $autoplay ) : ?>
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            <?php endif; ?>
            keyboard: { enabled: true },
        });
    });
})();
</script>
