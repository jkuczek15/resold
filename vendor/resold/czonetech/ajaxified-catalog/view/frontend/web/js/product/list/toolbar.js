/**
 * Copyright © 2016 CzoneTechnologies. All rights reserved.
 * For support requests, contact
 * ashish@czonetechnologies.com
 */

var win = this;
define([
    "jquery",
    "jquery/ui",
    "Magento_Theme/js/view/messages",
    "ko",
    "Magento_Catalog/js/product/list/toolbar"
], function($, ui, messageComponent, ko) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.productListToolbarForm', $.mage.productListToolbarForm, {

        options:
        {
            modeControl: '[data-role="mode-switcher"]',
            directionControl: '[data-role="direction-switcher"]',
            orderControl: '[data-role="sorter"]',
            limitControl: '[data-role="limiter"]',
            pagerControl: '[data-role="pager"], .pages-items a',
            mode: 'product_list_mode',
            direction: 'product_list_dir',
            order: 'product_list_order',
            limit: 'product_list_limit',
            pager: 'p',
            modeDefault: 'grid',
            directionDefault: 'desc',
            orderDefault: 'position',
            limitDefault: '9',
            pagerDefault: '1',
            productsToolbarControl:'.toolbar.toolbar-products',
            productsListBlock: '.products.wrapper',
            layeredNavigationFilterBlock: '.block.filter',
            filterItemControl: '.block.filter .item a, .block.filter .filter-clear,.block.filter .swatch-option-link-layered',
            url: '',
        },

        local_id: 231,
        position: null,

        _create: function () {
            this._super();
            this._bind($(this.options.pagerControl), this.options.pager, this.options.pagerDefault);
            $(this.options.filterItemControl)
                .off('click.'+this.namespace+'productListToolbarForm')
                .on('click.'+this.namespace+'productListToolbarForm', {}, $.proxy(this.applyFilterToProductsList, this))
            ;
            $('.filter-options-content').show();
        },
        _bind: function (element, paramName, defaultValue) {
            /**
             * Prevent double binding of these events because this component is being applied twice in the UI
             */
            if (element.is("select")) {
                element
                    .off('change.'+this.namespace+'productListToolbarForm')
                    .on('change.'+this.namespace+'productListToolbarForm', {paramName: paramName, default: defaultValue}, $.proxy(this._processSelect, this));
            } else {
                element
                    .off('click.'+this.namespace+'productListToolbarForm')
                    .on('click.'+this.namespace+'productListToolbarForm', {paramName: paramName, default: defaultValue}, $.proxy(this._processLink, this));
            }
        },
        applyFilterToProductsList: function (evt) {
            var link = $(evt.currentTarget);
            var urlParts = link.attr('href').split('?');
            var self = this;

            if(urlParts[1] != undefined && urlParts[1].includes(`local_global=${this.local_id}`)){
              if(this.position == null){
                // filter by local only, get the user's location
                navigator.geolocation.getCurrentPosition(function(position) {
                  // update the product collection
                  self.makeAjaxCall(urlParts[0], urlParts[1], position.coords.latitude, position.coords.longitude);
                }, function() {
                  alert('You must give Resold access to your location to view posts locally. Please allow location permissions by clicking the lock in the top left corner of the browser.');
                  handleLocationError(true, infoWindow, map.getCenter());
                });
              }else{
                self.makeAjaxCall(urlParts[0], urlParts[1], this.position.latitude, this.position.longitude);
              }
            }else{
              self.makeAjaxCall(urlParts[0], urlParts[1], null, null);
            }

            evt.preventDefault();
        },
        updateUrl: function (url, paramData) {
            if (!url) {
                return;
            }

            let setParams = false;
            url = url.substring(0, url.indexOf('?'));
            if (paramData && paramData.length > 0) {
                paramData = paramData.replace('longitude=&', '');
                paramData = paramData.replace('latitude=&', '');
                paramData = paramData.replace('local=true%2F&', '');
                url += '?' + paramData;
                setParams = true;
            }

            let search = $('#urlSearch').val();
            if(search != ''){
              if(setParams){
                url += `&qc=${search}`;
              }else{
                url += `?qc=${search}`;
              }
            }// end if search not null

            if (typeof history.replaceState === 'function') {
                history.replaceState(null, null, url);
            }
        },

        getParams: function (urlParams, paramName, paramValue, defaultValue) {
            var paramData = {},
                parameters;

            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                if (parameters[1] !== undefined) {
                    paramData[parameters[0]] = parameters[1];
                } else {
                    paramData[parameters[0]] = '';
                }
            }

            paramData[paramName] = paramValue;
            if (paramValue == defaultValue) {
                delete paramData[paramName];
            }
            return window.decodeURIComponent($.param(paramData).replace(/\+/g, '%20'));
        },
        _updateContent: function (content) {
            $(this.options.productsToolbarControl).remove();
            if(content.products_list){
                $(this.options.productsListBlock)
                    .replaceWith(content.products_list)
                ;
            }

            if(content.filters){
                $(this.options.layeredNavigationFilterBlock).replaceWith(content.filters)
            }

            $('body').trigger('contentUpdated');
        },

        updateContent: function (content) {
            $('html, body').animate(
                {
                    scrollTop: $(this.options.productsToolbarControl+":first").offset().top
                },
                100,
                'swing',
                this._updateContent(content)
            );
        },

        updatePlace: function(longitude, latitude, paramData){

            if(paramData == undefined || !paramData.includes(`local_global=${this.local_id}`)){
              $('#location-city').html('');
              return;
            }
            if(win.sessionStorage.place != undefined){
              // location already stored with local storage
              $('#location-city').html(`- 50 miles from ${win.sessionStorage.place}`);
            }else{
              // get the user's city from mapbox
              let api_key = 'pk.eyJ1Ijoiamt1Y3playIsImEiOiJjamxlZ2kyMzYwMnhsM3ByazM1ZWtibzllIn0.hsE3V5wLucE2wl8jdQhfTQ';
              let mapbox_url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${longitude},${latitude}.json?access_token=${api_key}`;
              $.get(mapbox_url, (data) => {

                  if(data && data.features && data.features.length > 0){

                      let place = '';
                      for(let feature of data.features){
                          if(feature.place_type.includes("place")){
                            place = feature.place_name;
                            break;
                          }// end if place type is place
                      }// end for loop over features

                      $('#location-city').html(`- 50 miles from ${place}`);
                      win.sessionStorage.place = place;
                  }
              });
            }
        },

        updateHidden(longitude, latitude, paramData){
          var filters = ['local_global', 'condition', 'latitude', 'longitude', 'price'];
          for(let filter of filters){
              let value = this.getQueryVariable(filter);
              $(`#${filter}`).val(value);
          }
        },

        changeUrl: function (paramName, paramValue, defaultValue) {
            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = this.getParams(urlParams, paramName, paramValue, defaultValue);

            this.makeAjaxCall(baseUrl, paramData);
        },

        makeAjaxCall: function (baseUrl, paramData, latitude, longitude) {
            var self = this;
            if(latitude != null && longitude != null){
              this.position = {latitude, longitude};
              paramData += `&latitude=${latitude}&longitude=${longitude}`;
            }
            let search = $('#urlSearch').val();
            if(search != ''){
              baseUrl += `?qc=${search}`;
            }// end if search not null

            $.ajax({
                url: baseUrl,
                data: (paramData && paramData.length > 0 ? paramData + '&ajax=1' : 'ajax=1'),
                type: 'get',
                dataType: 'json',
                cache: true,
                showLoader: true,
                timeout: 10000
            }).done(function (response) {
                if (response.success) {
                    self.updateUrl(baseUrl, paramData);
                    self.updateContent(response.html);
                    self.updatePlace(longitude, latitude, paramData);
                    self.updateHidden(longitude, latitude, paramData);
                } else {
                    var msg = response.error_message;
                    if (msg) {
                        self.setMessage({
                            type: 'error',
                            text: msg
                        });
                    }
                }
            }).fail(function (error) {
                self.setMessage({
                    type: 'error',
                    text: 'Sorry, something went wrong. Please try again later.'
                });
            });
        },
        setMessage: function (obj) {
            var messages = ko.observableArray([obj]);
            messageComponent().messages({
                messages: messages
            });
        },
        getQueryVariable: function(variable) {
          var query = window.location.search.substring(1);
          var vars = query.split('&');
          for (var i = 0; i < vars.length; i++) {
              var pair = vars[i].split('=');
              if (decodeURIComponent(pair[0]) == variable) {
                  return decodeURIComponent(pair[1]);
              }
          }
          return '';
        }
    });

    return $.mage.productListToolbarForm;
});
