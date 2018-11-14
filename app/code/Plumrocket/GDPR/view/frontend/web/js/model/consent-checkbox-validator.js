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

define([
    'jquery',
    'mage/validation'
], function ($) {
    'use strict';

    var consentInputPath = '.payment-method._active div.prgdpr-consent-checkboxes input';

    return {
        /**
         * Validate prgdpr consents
         *
         * @returns {Boolean}
         */
        validate: function () {
            var isValid = true;

            if ($(consentInputPath).length === 0) {
                return true;
            }

            $(consentInputPath).each(function (index, element) {
                if (!$.validator.validateSingleElement(element, {
                    errorElement: 'div'
                })) {
                    isValid = false;
                }
            });

            return isValid;
        }
    };
});