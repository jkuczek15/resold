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
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            linksPurchasedSeparately: '0',
            useDefaultPrice: false,
            listens: {
                linksPurchasedSeparately: 'changeDisabledStatus',
                useDefaultPrice: 'changeDisabledStatus'
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            this.changeDisabledStatus();

            return this;
        },

        /**
         * Disable/enable price field
         */
        changeDisabledStatus: function () {
            if (this.linksPurchasedSeparately === '1') {
                if (this.useDefaultPrice) {
                    this.disabled(true);
                } else {
                    this.disabled(false);
                }
            } else {
                this.disabled(true);
            }
        }
    });
});
