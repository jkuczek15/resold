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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

/* jscs:disable */
/* eslint-disable */
define([
    'jquery',
    'mage/storage'
], function ($, storage) {
    'use strict';

    /**
     * @param {Object} config
     */
    return function (config) {
        $.ajax({
            url: config.loadUrl,
            data: {
                global: false,
                componentName: config.componentName
            },
            method: 'GET',
            showLoader: false,
            dataType: 'json',
            success: function(response) {
                if (response.html) {
                    $(config.loadContainer).html(response.html);
                    $(config.loadContainer).trigger('contentUpdated').applyBindings();
                }
            }
        });
    }
});