define([
    'Magento_Ui/js/form/element/ui-select'
], function (Component) {
    'use strict';

    var id = window.mgzReportsConfig.store_id ? window.mgzReportsConfig.store_id : 'all';

    return Component.extend({

    	defaults: {
    		value: id,
    		multiple: false,
    		options: window.mgzReportsConfig.stores,
    		template: 'Magezon_Core/store-switcher',

            exports: {
                value: '${ $.provider }:params.store_id'
            }
    	},

    	initConfig: function (config) {

            config['options'] = window.mgzReportsConfig.stores;

            this._super(config);

            return this;
    	}
    })
});