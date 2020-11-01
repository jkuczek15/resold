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
                if(itemOne.code == 'shipping'){
                  itemOne.title = "Information";
                  itemTwo.title = "Review & Payment";
                }else{
                  itemOne.title = "Review & Payment";
                  itemTwo.title = "Information";
                }
                return stepNavigator.sortItems(itemOne, itemTwo);
            },

            navigateTo: function(step) {
                if(step.code == 'shipping'){
                  if( !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                    $('.modal-custom').css('cssText', 'margin-top: 234px;');
                  }
                }
                stepNavigator.navigateTo(step.code);
            },

            isProcessed: function(item) {
                return stepNavigator.isProcessed(item.code);
            }
        });
    }
);
