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
/*jshint browser:true jquery:true expr:true*/
define([
    'jquery',
    'Magento_Catalog/catalog/type-events',
    'Magento_Catalog/js/product/weight-handler'
], function ($, productType, weight) {
    'use strict';

    return {

        /**
         * Constructor component
         */
        'Magento_Bundle/js/bundle-type-handler': function () {
            this.bindAll();
            this._initType();
        },

        /**
         * Bind all
         */
        bindAll: function () {
            $(document).on('changeTypeProduct', this._initType.bind(this));
        },

        /**
         * Init type
         * @private
         */
        _initType: function () {
            if (
                productType.type.real === 'bundle' &&
                productType.type.current !== 'bundle' &&
                !weight.isLocked()
            ) {
                weight.switchWeight();
            }
        }
    };
});
