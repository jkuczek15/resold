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

/*global alert:true*/

define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    return function (config) {
        var categoryForm = {
            options: {
                categoryIdSelector: 'input[name="id"]',
                categoryPathSelector: 'input[name="path"]',
                refreshUrl: config.refreshUrl
            },

            /**
             * Sending ajax to server to refresh field 'path'
             * @protected
             */
            refreshPath: function () {
                if (!$(this.options.categoryIdSelector)) {
                    return false;
                }
                $.ajax({
                    url: this.options.refreshUrl,
                    method: 'GET',
                    showLoader: true
                }).done(this._refreshPathSuccess.bind(this));
            },

            /**
             * Refresh field 'path' on ajax success
             * @param {Object} data
             * @private
             */
            _refreshPathSuccess: function (data) {
                if (data.error) {
                    alert({
                        content: data.message
                    });
                } else {
                    $(this.options.categoryIdSelector).val(data.id).change();
                    $(this.options.categoryPathSelector).val(data.path).change();
                }
            }
        };

        $('body').on('categoryMove.tree', $.proxy(categoryForm.refreshPath.bind(categoryForm), this));
    };
});
