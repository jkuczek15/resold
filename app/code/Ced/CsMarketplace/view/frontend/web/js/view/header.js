/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'mage/translate',

], function (Component, $, ko, _) {
    'use strict';
    return Component.extend({
        customData:{href:'','percent':''},
        defaults: {
        },

        /**
         * @override
         */
        initialize: function(options) {
            var self = this;
            this.customData['contentLoading'] = ko.observable(1);
            this.customData['href'] = ko.observable('');
            this.customData['percent'] = ko.observable(0);
            this.customData['notifications'] = ko.observableArray([]);
            this.customData['vendor'] = ko.observableArray([]);
            this.customData['getViewAllUrl'] = ko.observable(options.viewall);
            this.updateNotifications(options.url);
            return this._super();
        },
        updateNotifications: function(url){
            var self = this;
            $.getJSON(url,function(data){
                self.customData.percent(data.statistics.percent);
                self.customData.href(data.statistics.href);
                self.customData.contentLoading(0);
                self.customData.notifications(data.notifications);
                self.customData.vendor(data.vendor);
                
                setTimeout(function(){ self.updateNotifications(url); }, 60000);
            });
        }

    });
});