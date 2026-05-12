<?php
/**
 * Template: metabox-settings.php
 *
 * Variables available:
 * @var int      $post_id
 * @var string   $slides_count
 * @var string   $cpt
 * @var string   $taxonomy
 * @var array    $terms_sel
 * @var array    $acf_fields      Currently saved: [ ['key'=>..., 'label'=>...] ]
 * @var string   $posts_per_page
 * @var string   $autoplay
 * @var array    $all_cpts        WP_Post_Type objects keyed by slug
 */

defined( 'ABSPATH' ) || exit;

$tax_objects   = $cpt ? SCC_Metaboxes::get_taxonomies_for_cpt( $cpt ) : [];
$term_list     = ( $cpt && $taxonomy ) ? SCC_Metaboxes::get_terms_for_taxonomy( $taxonomy ) : [];
$acf_available = ( $cpt && function_exists( 'acf_get_field_groups' ) ) ? SCC_Metaboxes::get_acf_fields_for_cpt( $cpt ) : [];
?>

<div class="scc-metabox">

    <!-- ===================== SEKCJA: Wyświetlanie ===================== -->
    <div class="scc-section">
        <h3 class="scc-section-title"><?php esc_html_e( 'Wyświetlanie', 'swiper-cpt-carousel' ); ?></h3>

        <table class="scc-table">
            <tr>
                <th><label for="scc_slides_count"><?php esc_html_e( 'Liczba slajdów widocznych naraz', 'swiper-cpt-carousel' ); ?></label></th>
                <td>
                    <div class="scc-radio-group">
                        <?php foreach ( [ '1', '2', '3' ] as $val ) : ?>
                            <label class="scc-radio-label <?php echo $slides_count === $val ? 'is-active' : ''; ?>">
                                <input type="radio" name="scc_slides_count" value="<?php echo esc_attr( $val ); ?>"
                                    <?php checked( $slides_count, $val ); ?>>
                                <?php echo esc_html( $val ); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <th><label for="scc_posts_per_page"><?php esc_html_e( 'Maksymalna liczba postów', 'swiper-cpt-carousel' ); ?></label></th>
                <td>
                    <input type="number" id="scc_posts_per_page" name="scc_posts_per_page"
                        value="<?php echo esc_attr( $posts_per_page ); ?>" min="1" max="100" class="small-text">
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Autoodtwarzanie', 'swiper-cpt-carousel' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="scc_autoplay" value="1" <?php checked( $autoplay, '1' ); ?>>
                        <?php esc_html_e( 'Włącz autoodtwarzanie', 'swiper-cpt-carousel' ); ?>
                    </label>
                </td>
            </tr>
        </table>
    </div>

    <!-- ===================== SEKCJA: Źródło postów ===================== -->
    <div class="scc-section">
        <h3 class="scc-section-title"><?php esc_html_e( 'Źródło postów', 'swiper-cpt-carousel' ); ?></h3>

        <table class="scc-table">
            <tr>
                <th><label for="scc_cpt"><?php esc_html_e( 'Typ posta (CPT)', 'swiper-cpt-carousel' ); ?></label></th>
                <td>
                    <select id="scc_cpt" name="scc_cpt" class="scc-select">
                        <option value=""><?php esc_html_e( '— wybierz —', 'swiper-cpt-carousel' ); ?></option>
                        <?php foreach ( $all_cpts as $slug => $obj ) : ?>
                            <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $cpt, $slug ); ?>>
                                <?php echo esc_html( $obj->label . ' (' . $slug . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr id="scc_row_taxonomy" <?php echo empty( $tax_objects ) ? 'style="display:none"' : ''; ?>>
                <th><label for="scc_taxonomy"><?php esc_html_e( 'Filtruj po taksonomii', 'swiper-cpt-carousel' ); ?></label></th>
                <td>
                    <select id="scc_taxonomy" name="scc_taxonomy" class="scc-select">
                        <option value=""><?php esc_html_e( 'Wszystkie', 'swiper-cpt-carousel' ); ?></option>
                        <?php foreach ( $tax_objects as $tax ) : ?>
                            <option value="<?php echo esc_attr( $tax->name ); ?>" <?php selected( $taxonomy, $tax->name ); ?>>
                                <?php echo esc_html( $tax->label . ' (' . $tax->name . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr id="scc_row_terms" <?php echo empty( $term_list ) ? 'style="display:none"' : ''; ?>>
                <th><?php esc_html_e( 'Kategorie / Terminy', 'swiper-cpt-carousel' ); ?></th>
                <td>
                    <div id="scc_terms_wrapper" class="scc-checklist">
                        <label class="scc-check-all">
                            <input type="checkbox" id="scc_terms_all" <?php echo empty( $terms_sel ) ? 'checked' : ''; ?>>
                            <strong><?php esc_html_e( 'Wszystkie', 'swiper-cpt-carousel' ); ?></strong>
                        </label>
                        <?php foreach ( $term_list as $term ) : ?>
                            <label>
                                <input type="checkbox" name="scc_terms[]"
                                    value="<?php echo esc_attr( $term->term_id ); ?>"
                                    <?php checked( in_array( $term->term_id, $terms_sel, true ) ); ?>>
                                <?php echo esc_html( $term->name ); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ===================== SEKCJA: Sortowanie ===================== -->
    <div class="scc-section">
        <h3 class="scc-section-title"><?php esc_html_e( 'Sortowanie', 'swiper-cpt-carousel' ); ?></h3>
        <table class="scc-table">
            <tr>
                <th><label for="scc_orderby"><?php esc_html_e( 'Sortuj po', 'swiper-cpt-carousel' ); ?></label></th>
                <td>
                    <?php
                    $orderby = get_post_meta( $post->ID, '_scc_orderby', true ) ?: 'date';
                    $orderby_options = [
                        'date'          => __( 'Data publikacji', 'swiper-cpt-carousel' ),
                        'title'         => __( 'Tytuł', 'swiper-cpt-carousel' ),
                        'modified'      => __( 'Data modyfikacji', 'swiper-cpt-carousel' ),
                        'comment_count' => __( 'Najczęściej komentowane', 'swiper-cpt-carousel' ),
                        'views'         => __( 'Najpopularniejsze (liczba odsłon)', 'swiper-cpt-carousel' ),
                        'rating'        => __( 'Najlepiej oceniane', 'swiper-cpt-carousel' ),
                        'rand'          => __( 'Losowo', 'swiper-cpt-carousel' ),
                        'menu_order'    => __( 'Kolejność menu', 'swiper-cpt-carousel' ),
                    ];
                    ?>
                    <select id="scc_orderby" name="scc_orderby" class="scc-select">
                        <?php foreach ( $orderby_options as $val => $label ) : ?>
                            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $orderby, $val ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr id="scc_row_order">
                <th><label for="scc_order"><?php esc_html_e( 'Kierunek', 'swiper-cpt-carousel' ); ?></label></th>
                <td>
                    <?php $order = get_post_meta( $post->ID, '_scc_order', true ) ?: 'DESC'; ?>
                    <select id="scc_order" name="scc_order" class="scc-select">
                        <option value="DESC" <?php selected( $order, 'DESC' ); ?>><?php esc_html_e( 'Malejąco (DESC)', 'swiper-cpt-carousel' ); ?></option>
                        <option value="ASC"  <?php selected( $order, 'ASC' ); ?>><?php esc_html_e( 'Rosnąco (ASC)', 'swiper-cpt-carousel' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr id="scc_row_views_meta" style="display:none">
                <th><?php esc_html_e( 'Meta key (odsłony)', 'swiper-cpt-carousel' ); ?></th>
                <td>
                    <?php $views_meta_key = get_post_meta( $post->ID, '_scc_views_meta_key', true ) ?: 'post_views_count'; ?>
                    <input type="text" name="scc_views_meta_key"
                        value="<?php echo esc_attr( $views_meta_key ); ?>"
                        class="regular-text"
                        placeholder="post_views_count">
                    <p class="description"><?php esc_html_e( 'Klucz meta przechowujący liczbę odsłon. Domyślnie: post_views_count', 'swiper-cpt-carousel' ); ?></p>
                </td>
            </tr>
            <tr id="scc_row_rating_meta" style="display:none">
                <th><?php esc_html_e( 'Meta key (ocena)', 'swiper-cpt-carousel' ); ?></th>
                <td>
                    <?php $rating_meta_key = get_post_meta( $post->ID, '_scc_rating_meta_key', true ) ?: 'ratings_average'; ?>
                    <input type="text" name="scc_rating_meta_key"
                        value="<?php echo esc_attr( $rating_meta_key ); ?>"
                        class="regular-text"
                        placeholder="ratings_average">
                    <p class="description">
                        <?php esc_html_e( 'Klucz meta z oceną. WP-PostRatings: ratings_average | KK Star Ratings: _kksr_avg', 'swiper-cpt-carousel' ); ?>
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <!-- ===================== SEKCJA: Pola ACF ===================== -->
    <div class="scc-section">
        <h3 class="scc-section-title">
            <?php esc_html_e( 'Dodatkowe pola ACF na slajdzie', 'swiper-cpt-carousel' ); ?>
            <?php if ( ! function_exists( 'acf_get_field_groups' ) ) : ?>
                <span class="scc-badge scc-badge--warning"><?php esc_html_e( 'ACF nieaktywne', 'swiper-cpt-carousel' ); ?></span>
            <?php endif; ?>
        </h3>

        <p class="description">
            <?php esc_html_e( 'Wybierz pola z listy (wymaga wybranego CPT) lub dodaj ręcznie przez wpisanie klucza pola i etykiety.', 'swiper-cpt-carousel' ); ?>
        </p>

        <div id="scc_acf_rows">
            <?php foreach ( $acf_fields as $i => $field ) : ?>
                <div class="scc-acf-row" data-index="<?php echo esc_attr( $i ); ?>">
                    <input type="text" name="scc_acf_fields[<?php echo $i; ?>][key]"
                        value="<?php echo esc_attr( $field['key'] ); ?>"
                        placeholder="<?php esc_attr_e( 'Klucz pola (np. cena)', 'swiper-cpt-carousel' ); ?>"
                        class="scc-acf-key">
                    <input type="text" name="scc_acf_fields[<?php echo $i; ?>][label]"
                        value="<?php echo esc_attr( $field['label'] ); ?>"
                        placeholder="<?php esc_attr_e( 'Etykieta (np. Cena)', 'swiper-cpt-carousel' ); ?>"
                        class="scc-acf-label">
                    <button type="button" class="button scc-remove-acf-row" title="<?php esc_attr_e( 'Usuń', 'swiper-cpt-carousel' ); ?>">✕</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="scc-acf-actions">
            <?php if ( ! empty( $acf_available ) ) : ?>
                <select id="scc_acf_detect">
                    <option value=""><?php esc_html_e( '— wybierz z listy —', 'swiper-cpt-carousel' ); ?></option>
                    <?php foreach ( $acf_available as $f ) : ?>
                        <option value="<?php echo esc_attr( $f['key'] ); ?>" data-label="<?php echo esc_attr( $f['label'] ); ?>">
                            <?php echo esc_html( $f['label'] . ' (' . $f['key'] . ')' ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="scc_add_from_acf" class="button">
                    <?php esc_html_e( '+ Dodaj z listy', 'swiper-cpt-carousel' ); ?>
                </button>
            <?php elseif ( $cpt && function_exists( 'acf_get_field_groups' ) ) : ?>
                <p class="description"><?php esc_html_e( 'Brak pól ACF przypisanych do tego CPT lub są to pola nieobsługiwane.', 'swiper-cpt-carousel' ); ?></p>
            <?php endif; ?>

            <button type="button" id="scc_add_acf_row" class="button">
                <?php esc_html_e( '+ Dodaj ręcznie', 'swiper-cpt-carousel' ); ?>
            </button>
        </div>

        <!-- Template row (hidden) -->
        <template id="scc_acf_row_tpl">
            <div class="scc-acf-row">
                <input type="text" name="scc_acf_fields[__INDEX__][key]"
                    placeholder="<?php esc_attr_e( 'Klucz pola', 'swiper-cpt-carousel' ); ?>" class="scc-acf-key">
                <input type="text" name="scc_acf_fields[__INDEX__][label]"
                    placeholder="<?php esc_attr_e( 'Etykieta', 'swiper-cpt-carousel' ); ?>" class="scc-acf-label">
                <button type="button" class="button scc-remove-acf-row" title="Usuń">✕</button>
            </div>
        </template>
    </div>

    <script>
    (function () {
        const orderby = document.getElementById('scc_orderby');
        if (!orderby) return;

        function toggleMetaRows() {
            const val = orderby.value;
            document.getElementById('scc_row_views_meta').style.display  = val === 'views'  ? '' : 'none';
            document.getElementById('scc_row_rating_meta').style.display = val === 'rating' ? '' : 'none';
            // Ukryj "kierunek" dla rand
            document.getElementById('scc_row_order').style.display = val === 'rand' ? 'none' : '';
        }

        orderby.addEventListener('change', toggleMetaRows);
        toggleMetaRows();
    })();
    </script>

</div><!-- .scc-metabox -->
