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
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'mage/translate'
], function (Component, $, ko, _) {
    'use strict';
    return Component.extend({
        staticsData:{},

        /**
         * @override
         */
        initialize: function(options) {
            var self = this;
            $.getJSON(options.url,function(data){
                _.each(data, function (value, key) {
                    if (!self.staticsData.hasOwnProperty(key)) {
                        self.staticsData[key] = ko.observable();
                    }
                    self.staticsData[key](value);
                }, this);
            });
            return this._super();
        },

        getStaticsParam: function (name) {
            if (!_.isUndefined(name)) {
                if (!this.staticsData.hasOwnProperty(name)) {
                    this.staticsData[name] = ko.observable();
                }
            }
            return this.staticsData[name]();
        },

        viewAction: function (action){
            if(action){
                window.location = action;
            }
        },
    });
});
