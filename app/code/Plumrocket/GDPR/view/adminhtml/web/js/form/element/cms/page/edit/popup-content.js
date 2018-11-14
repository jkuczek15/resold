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

/**
 * Single Checkbox Component Extended
 * @method extend(jsonObject)
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/wysiwyg'
], function (_, uiRegistry, wysiwyg) {
    'use strict';

    return wysiwyg.extend({
        defaults: {
            elementSelector: 'textarea',
            value: '',
            $wysiwygEditorButton: '',
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            template: 'ui/form/field',
            elementTmpl: 'ui/form/element/wysiwyg',
            content:        '',
            showSpinner:    false,
            loading:        false,
            disabled:       true,
            listens: {
                disabled: 'setDisabled'
            }
        },

        /**
         * Initialize handler.
         *
         * @param {String} value
         */
        initialize: function () {
            this._super();

            if (! this.value()) {
                this.value(this.default);
            }

            var interval = setInterval(function () {
                var popupContentField = uiRegistry.get('index = popup_content');

                if (popupContentField
                    && typeof popupContentField !== 'undefined'
                    && typeof popupContentField.$wysiwygEditorButton === 'object'
                ) {
                    var notifyViaPopupField = uiRegistry.get('index = notify_via_popup');

                    popupContentField.disabled(parseInt(notifyViaPopupField.value()) !== 1);
                    window.clearInterval(interval);
                }
            }, 1000);

            return this;
        }
    });
});