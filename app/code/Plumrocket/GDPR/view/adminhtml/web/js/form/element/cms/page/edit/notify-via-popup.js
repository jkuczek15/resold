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
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';

    var initDependencies = function (value) {
        value = parseInt(value) === 1;
        var popupContentField = uiRegistry.get('index = popup_content');

        if (popupContentField && typeof popupContentField !== 'undefined') {
            popupContentField.disabled(! value);
        }
    };

    return select.extend({
        // /**
        //  * Initialize handler.
        //  *
        //  * @param {String} value
        //  */
        // initialize: function () {
        //     this._super();
        //     initDependencies(this.getInitialValue());
        //
        //     return this;
        // },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function () {
            initDependencies(this.value());

            return this._super();
        }
    });
});