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
    'knockout'
], function (Abstract, ko) {
    'use strict';

    return Abstract.extend({

        /**
         * Initializes observable properties of instance
         *
         * @returns {Element} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe('checked');

            this.value = ko.pureComputed({

                /**
                 * use 'mappedValue' as value if checked
                 */
                read: function () {
                    return this.checked() ? this.mappedValue : '';
                },

                /**
                 * any value made checkbox checked
                 */
                write: function (val) {
                    if (val) {
                        this.checked(true);
                    }
                },
                owner: this
            });

            return this;
        }
    });
});
