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
    'Magento_Ui/js/grid/paging/paging',
    'underscore'
], function (Paging, _) {
    'use strict';

    return Paging.extend({
        defaults: {
            totalTmpl: 'Magento_Catalog/attributes/grid/paging',
            modules: {
                selectionColumn: '${ $.selectProvider }'
            },
            listens: {
                '${ $.selectProvider }:selected': 'changeLabel'
            },
            label: '',
            selectedAttrs: []
        },

        /**
         * Change label.
         *
         * @param {Array} selected
         */
        changeLabel: function (selected) {
            this.selectedAttrs = [];
            _.each(this.selectionColumn().rows(), function (row) {
                if (selected.indexOf(row['attribute_id']) !== -1) {
                    this.selectedAttrs.push(row['attribute_code']);
                }
            }, this);

            this.label(this.selectedAttrs.join(', '));
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super()
                .observe('label');

            return this;
        }
    });
});
