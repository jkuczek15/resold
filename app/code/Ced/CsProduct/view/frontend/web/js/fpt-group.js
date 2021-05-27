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
    'Magento_Ui/js/form/components/group',
    'uiRegistry',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate',
    'underscore'
], function (Group, uiRegistry, validation, $t, _) {
    'use strict';

    return Group.extend({
        defaults: {
            visible: true,
            label: '',
            showLabel: true,
            required: false,
            template: 'Ced_CsProduct/group/group',
            fieldTemplate: 'Ced_CsProduct/form/field',
            breakLine: true,
            validateWholeGroup: false,
            additionalClasses: {}
        },

        /** @inheritdoc */
        initialize: function () {
            validation.addRule('validate-fpt-group', function (value) {
                if (value.indexOf('?') !== -1) {

                    return false;
                }

                return true;
            }, $t('You must set unique country-state combinations within the same fixed product tax'));

            this._super();
        },

        /**
         *
         * @private
         */
        _handleOptionsAvailability: function () {
            var parent,
                dup;

            dup = {};
            parent = uiRegistry.get(uiRegistry.get(this.parentName).parentName);
            _.each(parent.elems(), function (elem) {
                var country,
                    state,
                    val,
                    key;

                country = uiRegistry.get(elem.name + '.countryState.country');
                state = uiRegistry.get(elem.name + '.countryState.state');
                val = uiRegistry.get(elem.name + '.countryState.val');

                key = country.value() + (state.value() > 0 ? state.value() : 0);
                dup[key]++;

                if (!dup[key]) {
                    dup[key] = 1;
                    val.value('');
                } else {
                    dup[key]++;
                    val.value(country.value() + '?' + country.name);
                }
            });
        },

        /** @inheritdoc */
        initElement: function (elem) {
            var obj;

            obj = this;
            this._super();
            elem.on('value', function () {
                obj._handleOptionsAvailability();
            });

            return this;
        }
    });
});
