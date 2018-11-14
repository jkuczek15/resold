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

/*global alert*/
define([
    'jquery'
], function ($) {
    'use strict';

    /** Override default place order action and add agreement_ids to request */
    return function (paymentData) {

        var consentCheckboxes = $('.payment-method._active div.prgdpr-consent-checkboxes input');

        if (!consentCheckboxes.length) {
            return;
        }

        var consentData = consentCheckboxes.serializeArray();
        var consentIds = [];

        consentData.forEach(function (item) {
            consentIds.push(item.value);
        });

        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        paymentData['extension_attributes']['consent_ids'] = consentIds;
    };
});