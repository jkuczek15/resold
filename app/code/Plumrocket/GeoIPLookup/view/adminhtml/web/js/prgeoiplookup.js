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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

require([
    'jquery',
    'mage/storage',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'domReady!'
], function ($, storage, __, alert) {
    'use strict';

    window.runInstallation = function (serviceId, urlAutomatic, urlManual, urlProgress) {
        var resultId = serviceId + '_status';
        var timerProgress = null;
        var installMethod = $('#' + serviceId + '_install_method').val();

        var url = urlAutomatic;
        if (installMethod === 'manual') {
            url = urlManual;
        }

        var enableSelect = function() {
            var selectId = serviceId + '_enabled',
            tooltip = serviceId + '_tooltip',
            tooltipElement = $('#' + tooltip);

            $('#' + selectId).prop('disabled', false);
            if (tooltipElement) {
                tooltipElement.remove();
            }
        };

        var updateStatus = function(data) {
            var percent = '0%';
            var color = 'green';
            var message = '';

            switch (data.status) {
                case "process":
                    $('#' + resultId + ' .status')[0].hide();
                    $('#' + resultId + ' .progress-container')[0].show();
                    percent = parseInt(parseInt(data.exec) * 100 / parseInt(data.total)) + '%';
                    message = data.message;
                    break;
                case "success":
                    window.clearInterval(timerProgress);
                    $('body').loader('hide');
                    $('.loading-mask').css('background', 'rgba(255, 255, 255, 0.4)');
                    $('#' + resultId + ' .progress-container')[0].hide();
                    $('#' + resultId + ' .status')[0].setStyle({color: color}).update(data.message).show();
                    enableSelect();
                    break;
                default:
                    window.clearInterval(timerProgress);
                    $('body').loader('hide');
                    $('.loading-mask').css('background', 'rgba(255, 255, 255, 0.4)');
                    $('#' + resultId + ' .progress-container')[0].hide();
                    color = 'red';
                    message = (typeof data.message !== "undefined") ? data.message : __("Import Failed!");
                    $('#' + resultId + ' .status')[0].setStyle({color: color}).update(message).show();
            }

            $('#' + resultId + ' .progress-container .progress-value')[0].setStyle({width: percent}).update(percent);
            $('#' + resultId + ' .progress-container .progress-title')[0].update(message);
            if ('alert' in data) {
                window.showAlert(data.error,data.alert);
            }
        }

        var getInstallationProgress = function() {
            storage.get(
                urlProgress
            ).done(function(response) {
                response = JSON.parse(response);
                if (response && response.status) {
                     updateStatus(response);
                }
            }).fail(function() {
                window.clearInterval(timerProgress);
                $('body').loader('hide');
                $('.loading-mask').css('background', 'rgba(255, 255, 255, 0.4)');
            });
            return true;
        };

        updateStatus({
            status: 'process',
            exec: 1,
            total: 100,
            message:  __('Installation in Progress...')
        });

        timerProgress = setInterval(getInstallationProgress, 3000);

        $('body').loader('show');
        $('.loading-mask').css('background', 'rgba(255, 255, 255, 0)');

        storage.get(
            url
        ).done(function(response) {
            response = JSON.parse(response);
            if (response) {
                updateStatus(response);
            }
        }).fail(function() {
            updateStatus({status: 'fail'});
        });
    };

    window.prGeoIptestApiConnection = function (serviceUrl, serviceId) {
        var button = $('#' + serviceId),
            resultContainerId = serviceId + '_result',
            resultText = '',
            resultStyle = '',
            resultClass = '';

        $.ajax({
            showLoader: true,
            url: window.location.protocol + "//" + serviceUrl,
            type: "GET",
            dataType: 'JSON'
        }).done(function() {
            resultText = __('Connection Successful!');
            resultStyle = 'color: green;';
            resultClass = 'message-success success';
        }).fail(function() {
            resultText = __('Connection Error!');
            resultStyle = 'color: red;';
            resultClass = 'message-error error';
        }).always(function() {
            if (!$('#' + resultContainerId).length) {
                button.after(
                    '<div id="' + resultContainerId + '" class="message '
                    + resultClass
                    + '" style="background: none;'
                    + resultStyle
                    + '">'
                    + resultText
                    + '</div>'
                );
            }
        });
    };

    window.geoipTest = function (testUrl) {
        var ip = $('#prgeoiplookup_geoiptest_ip_address').val(),
            url = testUrl + 'ip/' + ip,
            geoInfo = $('#prgeoiplookup_geoiptest_geoinfo'),
            map = $('#prgeoiplookup_google_map'),
            mapUrl = 'https://maps.google.com/?q=',
            apiTestEl = $('#rest_ip_test'),
            restApiUrl = $('#hidden_field_api_url').text();

        $('body').loader('show');
        storage.get(
            url
        ).done(function(response) {
            response = JSON.parse(response);
            geoInfo.val(response.text);
            apiTestEl.attr('href', restApiUrl + ip);
            apiTestEl.text(restApiUrl + ip);
            if (response.latitude && response.longitude) {
                map.attr('href', mapUrl + response.latitude + ',' + response.longitude);
            } else if (response.country_name) {
                map.attr('href', mapUrl + response.country_name);
            }
            var prefix = 'row_prgeoiplookup_geoiptest';
            $('#' + prefix + '_rest_ip').show();
            $('#' + prefix + '_geoinfo').show();
            $('#' + prefix + '_location_on_map').show();
        }).fail(function() {
        }).always(function() {
            $('body').loader('hide');
        });
    };

    $('#prgeoiplookup_geoiptest_geoinfo').val('');

    window.showAlert = function (title, message) {
        alert({
            title: title,
            content: message
        });
    };
});