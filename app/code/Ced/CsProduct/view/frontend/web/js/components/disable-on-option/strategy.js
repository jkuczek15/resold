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
define(function () {
    'use strict';

    return {
        defaults: {
            valuesForEnable: [],
            disabled: true,
            imports: {
                toggleDisable:
                    'product_attribute_add_form.product_attribute_add_form.base_fieldset.frontend_input:value'
            }
        },

        /**
         * Toggle disabled state.
         *
         * @param {Number} selected
         */
        toggleDisable: function (selected) {
            this.disabled(!(selected in this.valuesForEnable));
        }
    };
});
