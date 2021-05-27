/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
        'jquery',
        'underscore',
        'mageUtils',
        'uiClass',
        'mage/apply/main',
        'Magento_Ui/js/lib/knockout/bootstrap',
        'mage/mage'
], function ($, _, utils, Class) {
    'use strict';

    $.ajaxSetup({
        /*
         * @type {string}
         */
        type: 'POST',

        /*
         * Ajax before send callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {Object}
         */
        beforeSend: function(jqXHR, settings) {
            var form_key = typeof FORM_KEY !== 'undefined' ? FORM_KEY : null;
            if (!settings.url.match(new RegExp('[?&]isAjax=true',''))) {
                settings.url = settings.url.match(
                    new RegExp('\\?',"g")) ?
                    settings.url + '&isAjax=true' :
                    settings.url + '?isAjax=true';
            }
            if (!settings.data) {
                settings.data = {
                    form_key: form_key
                };
            } else if ($.type(settings.data) === "string" &&
                settings.data.indexOf('form_key=') === -1) {
                settings.data += '&' + $.param({
                    form_key: form_key
                });
            } else if($.isPlainObject(settings.data) && !settings.data.form_key) {
                settings.data.form_key = form_key;
            }
        },

        /*
         * Ajax complete callback
         * @param {Object} The jQuery XMLHttpRequest object returned by $.ajax()
         * @param {string}
         */
        complete: function(jqXHR) {
            if (jqXHR.readyState === 4) {
                try {
                    var jsonObject = $.parseJSON(jqXHR.responseText);
                    if (jsonObject.ajaxExpired && jsonObject.ajaxRedirect) {
                        window.location.replace(jsonObject.ajaxRedirect);
                    }
                } catch(e) {}
            }
        }
    });
    
    
    /**
     * Before save validate request.
     *
     * @param {Object} data
     * @param {String} url
     * @param {String} selectorPrefix
     * @param {String} messagesClass
     * @returns {*}
     */
    function beforeSave(data, url, selectorPrefix, messagesClass) {
        var save = $.Deferred();

        data = utils.serialize(data);

        data['form_key'] = window.FORM_KEY;

        if (!url || url === 'undefined') {
            return save.resolve();
        }
        $('body').trigger('processStart');
        $.ajax({
            url: url,
            data: data,
            type:"POST",

            /**
             * Success callback.
             * @param {Object} resp
             * @returns {Boolean}
             */
            success: function (resp) {
                if (!resp.error) {
                    save.resolve();

                    return true;
                }
                $(this.placeholder).html('');
                
                $( ".page" ).append('<div class="messages"><div class="message message-error error"><div data-ui-id="messages-message-error">'+resp.messages+'</div></div></div>');
            },

            /**
             * Complete callback.
             */
            complete: function () {
                $('body').trigger('processStop');
            }
        });

        return save.promise();
    }

    return Class.extend({

        /**
         * Assembles data and submits it using 'utils.submit' method
         */
        save: function (data, options) {
            var url = this.urls.beforeSave,
                save = this._save.bind(this, data, options);

            beforeSave(data, url, this.selectorPrefix, this.messagesClass).then(save);

            return this;
        },

        /**
         * Save data.
         *
         * @param {Object} data
         * @param {Object} options
         * @returns {Object}
         * @private
         */
        _save: function (data, options) {
            var url = this.urls.save;

            options = options || {};

            if (!options.redirect) {
                url += 'back/edit';
            }

            if (options.ajaxSave) {
                utils.ajaxSubmit({
                    url: url,
                    data: data,
                    type:"POST"
                }, options);

                return this;
            }

            utils.submit({
                url: url,
                data: data,
                type:"POST"
            }, options.attributes);

            return this;
        }
        
    });
});