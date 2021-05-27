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
    'Magento_Ui/js/form/components/fieldset',
    'Magento_Ui/js/core/app'
], function (Fieldset, app) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            listens: {
                '${ $.provider }:additionalAttributes': 'onAttributeAdd'
            }
        },

        /**
         * On attribute add trigger
         *
         * @param {Object} listOfNewAttributes
         */
        onAttributeAdd: function (listOfNewAttributes) {
            app(listOfNewAttributes, true);
        }
    });
});
