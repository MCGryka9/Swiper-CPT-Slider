/* global SCC_Admin, jQuery */
(function ($) {
    'use strict';

    // -----------------------------------------------------------------------
    // DOM refs
    // -----------------------------------------------------------------------
    const $cptSelect  = $('#scc_cpt');
    const $taxRow     = $('#scc_row_taxonomy');
    const $taxSelect  = $('#scc_taxonomy');
    const $termsRow   = $('#scc_row_terms');
    const $termsWrap  = $('#scc_terms_wrapper');
    const $termsAll   = $('#scc_terms_all');
    const $acfRows    = $('#scc_acf_rows');
    const $acfTpl     = $('#scc_acf_row_tpl');
    const $acfDetect  = $('#scc_acf_detect');
    const radioLabels = $('.scc-radio-label');

    // -----------------------------------------------------------------------
    // Radio: sync active class
    // -----------------------------------------------------------------------
    radioLabels.on('change', 'input[type="radio"]', function () {
        radioLabels.removeClass('is-active');
        $(this).closest('.scc-radio-label').addClass('is-active');
    });

    // -----------------------------------------------------------------------
    // CPT → load taxonomies + ACF fields
    // -----------------------------------------------------------------------
    $cptSelect.on('change', function () {
        const cpt = $(this).val();

        // Reset dependent controls
        $taxRow.hide();
        $taxSelect.html('<option value="">' + sccL10n('loading') + '</option>');
        $termsRow.hide();
        $termsWrap.html('');
        $acfDetect.html('<option value="">\u2014 ' + sccL10n('select') + ' \u2014</option>').parent().hide();

        if (!cpt) return;

        // --- Taxonomies ---
        $.post(SCC_Admin.ajax_url, {
            action: 'scc_get_taxonomies',
            nonce:  SCC_Admin.nonce,
            cpt:    cpt,
        }, function (res) {
            if (!res.success || !res.data.length) {
                $taxRow.hide();
                return;
            }
            let opts = '<option value="">Wszystkie</option>';
            res.data.forEach(function (t) {
                opts += `<option value="${esc(t.slug)}">${esc(t.label)} (${esc(t.slug)})</option>`;
            });
            $taxSelect.html(opts);
            $taxRow.show();
        });

        // --- ACF fields ---
        $.post(SCC_Admin.ajax_url, {
            action: 'scc_get_acf_fields',
            nonce:  SCC_Admin.nonce,
            cpt:    cpt,
        }, function (res) {
            if (!res.success || !res.data.length) return;
            let opts = `<option value="">\u2014 wybierz z listy \u2014</option>`;
            res.data.forEach(function (f) {
                opts += `<option value="${esc(f.key)}" data-label="${esc(f.label)}">${esc(f.label)} (${esc(f.key)})</option>`;
            });
            $acfDetect.html(opts).closest('.scc-acf-actions').show();
        });
    });

    // -----------------------------------------------------------------------
    // Taxonomy → load terms
    // -----------------------------------------------------------------------
    $taxSelect.on('change', function () {
        const taxonomy = $(this).val();
        $termsRow.hide();
        $termsWrap.html('');

        if (!taxonomy) return;

        $.post(SCC_Admin.ajax_url, {
            action:   'scc_get_terms',
            nonce:    SCC_Admin.nonce,
            taxonomy: taxonomy,
        }, function (res) {
            if (!res.success || !res.data.length) return;

            let html = `<label class="scc-check-all">
                <input type="checkbox" id="scc_terms_all" checked>
                <strong>Wszystkie</strong>
            </label>`;
            res.data.forEach(function (t) {
                html += `<label>
                    <input type="checkbox" name="scc_terms[]" value="${esc(t.id)}">
                    ${esc(t.name)}
                </label>`;
            });
            $termsWrap.html(html);
            $termsRow.show();
            bindTermsAll();
        });
    });

    // -----------------------------------------------------------------------
    // "Wszystkie" checkbox logic
    // -----------------------------------------------------------------------
    function bindTermsAll() {
        $termsWrap.on('change', '#scc_terms_all', function () {
            if ($(this).is(':checked')) {
                $termsWrap.find('input[name="scc_terms[]"]').prop('checked', false);
            }
        });
        $termsWrap.on('change', 'input[name="scc_terms[]"]', function () {
            if ($termsWrap.find('input[name="scc_terms[]"]:checked').length) {
                $termsWrap.find('#scc_terms_all').prop('checked', false);
            }
        });
    }
    bindTermsAll();

    // -----------------------------------------------------------------------
    // ACF rows management
    // -----------------------------------------------------------------------
    let acfIndex = $acfRows.find('.scc-acf-row').length;

    function addAcfRow(key, label) {
        const tplHtml = $acfTpl.html()
            .replace(/__INDEX__/g, acfIndex);
        const $row = $(tplHtml);
        if (key)   $row.find('.scc-acf-key').val(key);
        if (label) $row.find('.scc-acf-label').val(label);
        $acfRows.append($row);
        acfIndex++;
    }

    // Add manually
    $('#scc_add_acf_row').on('click', function () {
        addAcfRow('', '');
    });

    // Add from ACF detect dropdown
    $('#scc_add_from_acf').on('click', function () {
        const $opt = $acfDetect.find('option:selected');
        const key  = $opt.val();
        if (!key) return;
        addAcfRow(key, $opt.data('label'));
        $acfDetect.val('');
    });

    // Remove row (delegated)
    $acfRows.on('click', '.scc-remove-acf-row', function () {
        $(this).closest('.scc-acf-row').remove();
    });

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------
    function esc(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    function sccL10n(key) {
        const map = { loading: 'Ładowanie…', select: 'wybierz' };
        return map[key] || key;
    }

}(jQuery));
