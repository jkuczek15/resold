define([
    'jquery',
    'ko',
    'underscore',
    'mage/translate',
    'Magento_Catalog/js/price-utils',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, ko, _, $t, priceUtils) {
    
    var Autocomplete = function (input) {
        this.$input = $(input);
        this.isVisible = false;
        this.isShowAll = true;
        this.loading = false;
        this.config = [];
        this.result = false
    };
    
    Autocomplete.prototype = {
        init: function (config) {
            this.config = _.defaults(config, this.defaults);
            window.priceFormat = this.config.priceFormat;
            
            this.doSearch = _.debounce(this._doSearch, this.config.delay);
            
            this.$input.after($('#searchAutocompletePlaceholder').html());
            
            this.placeholderSelector = '.searchautocomplete__autocomplete';
            
            this.wrapperSelector = '.wrapper';
            
            this.xhr = null;
            
            this.$input.on("click focus", function () {
                this.clickHandler()
            }.bind(this));
            
            this.$input.on("input", function () {
                this.inputHandler()
            }.bind(this));
            
            $(document).click(function (event) {
                this.clickHandler(event)
            }.bind(this));
            
            ko.bindingHandlers.highlight = {
                init: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                    var needle      = bindingContext.$parents[2].result.query,
                        haystack    = $(element).html(),
                        regEx       = new RegExp("\ " + needle, "ig"),
                        replaceMask = '<span class="searchautocomplete__highlight">' + needle.charAt(0) + needle.slice(1) + '</span>';
                    
                    $(element).html(haystack.replace(regEx, replaceMask));
                }
            };
            
            ko.bindingHandlers.price = {
                init: function (element) {
                    $(element).html(priceUtils.formatPrice($(element).html(), window.priceFormat));
                }
            }
        },
        
        clickHandler: function (event) {
            if (!event) {
                if (this.result) {
                    $('body').addClass('searchautocomplete__active');
                    this.$input.addClass('searchautocomplete__active');
                    this.$placeholder().addClass('_active');
                    setTimeout(function () { this.ensurePosition(); }.bind(this), 200);
                } else {
                    this.result = this.search();
                }
            } else {
                if (!$(event.target).closest(this.$input.parent()).length) {
                    $('body').removeClass('searchautocomplete__active');
                    this.$placeholder().removeClass('_active');
                }
            }

        },
        
        inputHandler: function () {
            $('body').addClass('searchautocomplete__active');
            
            this.result = this.search();
            
            setTimeout(function () {
                if (this.result) {
                    this.$placeholder().addClass('_active');
                } else {
                    this.$placeholder().removeClass('_active');
                }
            }.bind(this), 200);
            
            this.ensurePosition();
        },
        
        $spinner: function () {
            return this.$placeholder().find(".searchautocomplete__spinner");
        },
        
        search: function () {
            this.ensurePosition();
            
            this.$input.off("keydown");
            this.$input.off("blur");
            
            if (this.xhr != null) {
                this.xhr.abort();
                this.xhr = null;
            }
            
            if (this.$input.val().length >= this.config.minSearchLength) {
                this.doSearch();
            } else {
                return this.doPopular();
            }
            
            return true;
        },
        
        _doSearch: function () {
            this.isVisible = true;
            
            this.$spinner().show();
            
            this.xhr = $.ajax({
                url:      this.config.url,
                dataType: 'json',
                type:     'GET',
                data:     {
                    q:   this.$input.val(),
                    cat: false
                },
                success:  function (data) {
                    this.processApplyBinding(data);
                    
                    this.$spinner().hide();
                }.bind(this)
            });
        },
        
        viewModel: function (data) {
            var model = {
                onMouseOver: function (item, event) {
                    $(event.currentTarget).addClass('_active');
                }.bind(this),
                
                onMouseOut: function (item, event) {
                    $(event.currentTarget).removeClass('_active');
                }.bind(this),
                
                afterRender: function (el) {
                    $(el).catalogAddToCart({});
                }.bind(this),
                
                onClick: function (item, event) {
                    if (event.button === 0) { // left click
                        event.preventDefault();
                        
                        if ($(event.target).closest('.tocart').length) {
                            return;
                        }
                        
                        if (event.target.nodeName === 'A'
                            || event.target.nodeName === 'IMG'
                            || event.target.nodeName === 'LI'
                            || event.target.nodeName === 'SPAN'
                            || event.target.nodeName === 'DIV') {
                            this.enter(item);
                        }
                    }
                }.bind(this),
                
                onSubmit: function (item, event) {
                }.bind(this),
                
                bindPrice: function (item, event) {
                    return true;
                }.bind(this)
            };
            
            model.isVisible = this.isVisible;
            model.loading = this.loading;
            model.result = data;
            model.result.isShowAll = this.isShowAll;
            model.form_key = $.cookie('form_key');
            
            return model;
        },
        
        enter: function (item) {
            if (item.url) {
                window.location.href = item.url;
            } else {
                this.pasteToSearchString(item.query);
            }
        },
        
        pasteToSearchString: function (searchTerm) {
            this.$input.val(searchTerm);
            this.search();
        },
        
        doPopular: function () {
            if (this.config.popularSearches.length) {
                this.processApplyBinding(this._showQueries(this.config.popularSearches));
                
                return true;
            }
            
            return false;
        },
        
        processApplyBinding: function (data) {
            if (this.$wrapper().length > 0) {
                if (!!ko.dataFor(this.$wrapper())) {
                    ko.cleanNode(this.$wrapper());
                }
            }
            
            this.$wrapper().remove();
            
            var wrapper = $('#searchAutocompleteWrapper').html();
            
            this.$placeholder().append(wrapper);

            ko.applyBindings(this.viewModel(data), this.$wrapper()[0]);
            
            this.ensurePosition();
        },

        $placeholder: function () {
            return $(this.$input.next(this.placeholderSelector));
        },

        $wrapper: function () {
            return $(this.$input.next(this.placeholderSelector).find(this.wrapperSelector));
        },
        
        _showQueries: function (data) {
            var self = this;
            var queries = data;
            var items = [];
            var item;
            var result, index;
            
            _.each(queries, function (query, idx) {
                if (idx < 5) {
                    item = {};
                    item.query = query;
                    item.enter = function () {
                        self.query = query;
                    };
                    
                    items.push(item);
                }
            }, this);
            
            result = {
                totalItems: items.length,
                query:      this.$input.val(),
                indices:    [],
                isShowAll:  false
            };
            
            index = {
                totalItems:   items.length,
                isShowTotals: false,
                items:        items,
                identifier:   'popular',
                title:        $t('Hot Searches')
            };
            
            result.indices.push(index);
            
            return result;
        },
        
        ensurePosition: function () {
            var position = this.$input.position();
            var left = position.left + parseInt(this.$input.css('marginLeft'), 10);
            var top = position.top + parseInt(this.$input.css('marginTop'), 10);
            
            $(this.placeholderSelector)
                .css('top', this.$input.outerHeight() - 1 + top)
                .css('left', left)
                .css('width', this.$input.outerWidth());
        }
    };
    
    return Autocomplete;
});
