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
    'Magento_Ui/js/form/components/html'
], function (Html) {
    'use strict';

    return Html.extend({
        defaults: {
            form: '${ $.namespace }.${ $.namespace }',
            visible: false,
            imports: {
                responseData: '${ $.form }:responseData',
                visible: 'responseData.error',
                content: 'responseData.messages'
            },
            listens: {
                '${ $.provider }:data.reset': 'hide'
            }
        },

        /**
         * Show messages.
         */
        show: function () {
            this.visible(true);
        },

        /**
         * Hide messages.
         */
        hide: function () {
            this.visible(false);
        }
    });
});
