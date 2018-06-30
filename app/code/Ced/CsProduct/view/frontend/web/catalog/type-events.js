/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return {
        $type: $('#product_type_id'),

        /**
         * Init
         */
        init: function () {
            this.type = {
                init: this.$type.val(),
                current: this.$type.val()
            };

            this.bindAll();
        },

        /**
         * Bind all
         */
        bindAll: function () {
            $(document).on('setTypeProduct', function (event, type) {
                this.setType(type);
            }.bind(this));

            //direct change type input
            this.$type.on('change', function () {
                this.type.current = this.$type.val();
                this._notifyType();
            }.bind(this));
        },

        /**
         * Set type
         * @param {String} type - type product (downloadable, simple, virtual ...)
         * @returns {*}
         */
        setType: function (type) {
            return this.$type.val(type || this.type.init).trigger('change');
        },

        /**
         * Notify type
         * @private
         */
        _notifyType: function () {
            $(document).trigger('changeTypeProduct', this.type);
        }
    };
});
