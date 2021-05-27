/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

require([
    'jquery',
    'Plumrocket_GDPR/js/chosen.jquery.min',
    'domReady!'
], function($) {
    'use strict';

    /*Fix. It disable warning "jQuery.browser is deprecated"*/
    /*$.migrateMute = true;*/

    if (1 === $('fieldset#prgdpr_general').length) {
        /*Add row.*/
        var $checkboxesGrid = $('#row_prgdpr_consent_checkboxes_checkboxes > td.value .data-grid tbody');
        var checkboxesTemplate;
        var checkboxesTemplate2;
        var checkboxesTemplateInterval = setInterval(function() {
            if (0 === $checkboxesGrid.find('*[data-mage-init]').length) {
                var $inherit = $('#prgdpr_consent_checkboxes_checkboxes_inherit:checked');

                if ($inherit.length) {
                    $inherit.click();
                }

                checkboxesTemplate = $checkboxesGrid.find('tr:first-child').html();
                checkboxesTemplate2 = $checkboxesGrid.find('tr:nth-child(2)').html();
                $checkboxesGrid.find('tr:first-child').remove();
                $checkboxesGrid.find('tr:first-child').remove();
                $checkboxesGrid.addClass('loaded');

                if ($inherit.length) {
                    $inherit.click();
                }

                clearInterval(checkboxesTemplateInterval);
            }
        }, 200);

        $('#row_prgdpr_consent_checkboxes_checkboxes > td.value > .checkbox-add').on('click', function() {
            var name = 'checkbox-' + Date.now();
            var template = checkboxesTemplate.split('_TMPNAME_').join(name);
            var template2 = checkboxesTemplate2.split('_TMPNAME_').join(name);
            $checkboxesGrid.append('<tr>'+ template +'</tr><tr>' + template2 + '</tr>');
            initializeGeoIPSelects();

            return false;
        });

        /* Remove row. */
        $checkboxesGrid.on('click', '.checkbox-remove', function() {
            var currentRow = $(this).parent().parent();
            currentRow.next('tr').remove();
            currentRow.remove();
        });

        /* enable chosen */
        setTimeout(function() {
            /* Scope. */
            $('#prgdpr_consent_checkboxes_checkboxes_inherit:checked').click().click();
            $('#prgdpr_cookie_consent_geoip_restriction_inherit:checked').click().click();

            initializeGeoIPSelects();
        }, 2000);

        /* fix for chosen from not expanded section */
        $('#prgdpr_cookie_consent-head').on('click', function () {
            reinitializeGeoIPSelect(document.getElementById('prgdpr_cookie_consent_geoip_restriction'));
        });

        $('#prgdpr_consent_checkboxes-head').on('click', function () {
            reinitializeGeoIPSelects();
        });

        $('#prgdpr_cookie_consent_geoip_restriction_inherit').on('click', function () {
            reinitializeGeoIPSelect(document.getElementById('prgdpr_cookie_consent_geoip_restriction'));
        });

        $('#prgdpr_consent_checkboxes_checkboxes_inherit').on('click', function () {
            reinitializeGeoIPSelects();
        });

        /* Registry for all selects with class geoip-select-with-chosen */
        window.initializedSelects = [];
    }

    function initializeGeoIPSelects() {
        var selects = getGeoIPSelects();

        selects.forEach(function (select) {
            if (! isInitializedGeoIPSelect(select)) {
                initializeGeoIPSelect(select);
                initializeGeoIPOptionDepends(select);
            }
        });
    }

    function reinitializeGeoIPSelects() {
        var selects = getGeoIPSelects();

        selects.forEach(function (select) {
            reinitializeGeoIPSelect(select);
        });
    }

    function getGeoIPSelects() {
        var result = $('select.geoip-select-with-chosen');

        return result.length ? result.toArray() : [];
    }

    function initializeGeoIPSelect(select) {
        if (! select instanceof HTMLElement) {
            return false;
        }

        if (isInitializedGeoIPSelect(select)) {
            return getIndexOfRegisteredGeoIPSelect(select);
        }

        var enableSearch = true;

        if ("readonly" === select.getAttribute('readonly')) {
            enableSearch = false;
            //select.disabled = true;

            if (select.options instanceof HTMLCollection) {
                for (var i=0;i<select.options.length;i++) {
                    var option = select.options[i];

                    if ('all' !== option.value) {
                        //option.disabled = true;
                    }
                }
            }
        }

        $(select).chosen({
            "display_selected_options" : true,
            "display_disabled_options": true,
            "hide_results_on_select": true,
            "group_search": enableSearch
        });

        return registerGeoIPSelect(select);
    }

    function registerGeoIPSelect(select) {
        return initializedSelects.push(select.name);
    }

    function getIndexOfRegisteredGeoIPSelect(select)
    {
        return initializedSelects.indexOf(select.name);
    }

    function isInitializedGeoIPSelect(select) {
        return -1 !== getIndexOfRegisteredGeoIPSelect(select);
    }

    function reinitializeGeoIPSelect(select)
    {
        if (isInitializedGeoIPSelect(select)) {
            $(select).chosen("destroy");
            initializedSelects.splice(getIndexOfRegisteredGeoIPSelect(select), 1);
            initializeGeoIPSelect(select);
        }
    }

    function updateGeoIpSelect(select) {
        if (! isInitializedGeoIPSelect(select)) {
            initializeGeoIPSelect(select);
        }

        $(select).trigger("chosen:updated");
    }

    function initializeGeoIPOptionDepends(select) {
        initializeGeoIPSelect(select);

        var geoipEl = $(select),
            allOptionId = 'all';

        var initialValues = ! geoipEl.val() ? [] : geoipEl.val();
        var initialShowToAllIndex = initialValues.indexOf(allOptionId);

        geoipEl.on('change', function () {
            var values = ! $(this).val() ? [] : $(this).val();
            var showToAllIndex = values.indexOf(allOptionId);

            if (-1 !== initialShowToAllIndex) {
                if (-1 !== showToAllIndex && values.length > 1) {
                    for (var i=0;i<values.length;i++) {
                        values.splice(showToAllIndex, 1);
                    }

                    geoipEl.val(values);
                }
            } else {
                if (-1 !== showToAllIndex && values.length > 1) {
                    geoipEl.val([allOptionId]);
                }
            }

            if (! $(this).val()) {
                $(this).val([allOptionId]);
            }

            initialValues = $(this).val();
            initialShowToAllIndex = initialValues.indexOf(allOptionId);
            updateGeoIpSelect(select);
        });
    }
});