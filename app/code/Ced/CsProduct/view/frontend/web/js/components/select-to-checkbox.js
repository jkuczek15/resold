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
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({

        /**
         * Converts initial value to integer
         *
         * @returns {Abstract}
         */
        setInitialValue: function () {
            this._super();
            this.value(+this.value());

            return this;
        },

        /**
         * Converts new value to integer
         *
         * @returns {Boolean}
         */
        onUpdate: function () {
            this._super();
            this.value(+this.value());

            return this._super();
        }
    });
});
