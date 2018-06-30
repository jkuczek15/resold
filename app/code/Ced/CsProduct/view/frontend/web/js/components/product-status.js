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
    'Magento_Ui/js/form/element/abstract',
    'underscore'
], function (Abstract, _) {
    'use strict';

    return Abstract.extend({
        defaults: {
            'mappingValues': {
                '1': true,
                '2': false
            },
            'checked': false,
            'mappedValue': '',
            'links': {
                value: false,
                'mappedValue': '${ $.provider }:${ $.dataScope }'
            },
            imports: {
                checked: 'mappedValue'
            }
        },

        /**
         * @returns {*}
         */
        setMappedValue: function () {
            var newValue;

            _.some(this.mappingValues, function (item, key) {
                if (item === this.value()) {
                    newValue = key;

                    return true;
                }
            }, this);

            return newValue;
        },

        /**
         * @returns {*}
         */
        initObservable: function () {
            return this.observe('mappedValue checked')._super();
        },

        /**
         * @returns {*}
         */
        setInitialValue: function () {
            this.value(this.mappedValue());
            this._super();
            this.mappedValue(this.initialValue);
            this.value(this.mappingValues[this.initialValue]);
            this.initialValue = this.value();

            return this;
        },

        /**
         * @returns {*}
         */
        onUpdate: function () {
            this.mappedValue(this.setMappedValue());

            return this._super();
        }
    });
});
