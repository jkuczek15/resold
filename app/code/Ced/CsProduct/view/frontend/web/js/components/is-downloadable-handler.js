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
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            listens: {
                disabled: 'changeVisibility'
            },
            modules: {
                samplesFieldset: '${ $.samplesFieldset }',
                linksFieldset: '${ $.linksFieldset}'
            }
        },

        /**
         * Change visibility for samplesFieldset & linksFieldset based on current statuses of checkbox.
         */
        changeVisibility: function () {
            if (this.samplesFieldset() && this.linksFieldset()) {
                if (this.checked() && !this.disabled()) {
                    this.samplesFieldset().visible(true);
                    this.linksFieldset().visible(true);
                } else {
                    this.samplesFieldset().visible(false);
                    this.linksFieldset().visible(false);
                }
            }
        },

        /**
         * Handle checked state changes for checkbox / radio button.
         *
         * @param {Boolean} newChecked
         */
        onCheckedChanged: function (newChecked) {
            this.changeVisibility();
            this._super(newChecked);
        }
    });
});
