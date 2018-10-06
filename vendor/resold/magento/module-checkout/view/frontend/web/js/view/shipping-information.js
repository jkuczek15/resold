/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/sidebar'
    ],
    function($, Component, quote, stepNavigator, sidebarModel) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping-information'
            },

            isVisible: function() {
                return !quote.isVirtual() && stepNavigator.isProcessed('shipping');
            },

            getShippingMethodTitle: function() {
                var shippingMethod = quote.shippingMethod();
                var text = '&nbsp;'
                if(shippingMethod){
                  text = shippingMethod.carrier_title;
                }
                return text;
            },

            getSeller: function() {
              var seller = $('.values').html();
              return seller;
            },

            back: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping');
            },

            backToShippingMethod: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping', 'opc-shipping_method');
            }
        });
    }
);
