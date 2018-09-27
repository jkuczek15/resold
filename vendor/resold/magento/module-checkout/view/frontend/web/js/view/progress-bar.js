/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        "underscore",
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/step-navigator',
        'jquery/jquery.hashchange'
    ],
    function ($, _, ko, Component, stepNavigator) {
        var steps = stepNavigator.steps;

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/progress-bar',
                visible: true
            },
            steps: steps,

            initialize: function() {
                this._super();
                $(window).hashchange(_.bind(stepNavigator.handleHash, stepNavigator));
                stepNavigator.handleHash();
            },

            sortItems: function(itemOne, itemTwo) {
                return stepNavigator.sortItems(itemOne, itemTwo);
            },

            navigateTo: function(step) {
                if(step.code == 'shipping'){
                  $('.modal-content').css('margin-top', '0');
                }else{
                  $('.modal-content').css('margin-top', '-13.5em');
                }
                stepNavigator.navigateTo(step.code);
            },

            isProcessed: function(item) {
                return stepNavigator.isProcessed(item.code);
            }
        });
    }
);
