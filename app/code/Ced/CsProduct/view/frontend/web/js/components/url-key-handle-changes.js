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
    'Magento_Ui/js/form/element/single-checkbox'
], function (Checkbox) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            imports: {
                handleUseDefault: '${ $.parentName }.use_default.url_key:checked',
                urlKey: '${ $.provider }:data.url_key'
            },
            listens: {
                urlKey: 'handleChanges'
            },
            modules: {
                useDefault: '${ $.parentName }.use_default.url_key'
            }
        },

        /**
         * Disable checkbox field, when 'url_key' field without changes or 'use default' field is checked
         */
        handleChanges: function (newValue) {
            this.disabled(newValue === this.valueMap['true'] || this.useDefault.checked);
        },

        /**
         * Disable checkbox field, when 'url_key' field without changes or 'use default' field is checked
         */
        handleUseDefault: function (checkedUseDefault) {
            this.disabled(this.urlKey === this.valueMap['true'] || checkedUseDefault);
        }
    });
});
