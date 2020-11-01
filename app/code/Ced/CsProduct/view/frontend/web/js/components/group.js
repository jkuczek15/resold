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
define([
    'underscore',
    'uiCollection'
], function (_, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            visible: true,
            label: '',
            showLabel: true,
            required: false,
            template: 'csproduct/group/group',
            fieldTemplate: 'csproduct/form/field',
            breakLine: true,
            validateWholeGroup: false,
            additionalClasses: {}
        },

        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         */
        initialize: function () {
            this._super()
                ._setClasses();

            return this;
        },

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         *
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this._super()
                .observe('visible')
                .observe({
                    required: !!+this.required
                });

            return this;
        },

        /**
         * Extends 'additionalClasses' object.
         *
         * @returns {Group} Chainable.
         */
        _setClasses: function () {
            var addtional = this.additionalClasses,
                classes;

            if (_.isString(addtional)) {
                addtional = this.additionalClasses.split(' ');
                classes = this.additionalClasses = {};

                addtional.forEach(function (name) {
                    classes[name] = true;
                }, this);
            }

            _.extend(this.additionalClasses, {
                'admin__control-grouped': !this.breakLine,
                'admin__control-fields': this.breakLine,
                required:   this.required,
                _error:     this.error,
                _disabled:  this.disabled
            });

            return this;
        },

        /**
         * Defines if group has only one element.
         * @return {Boolean}
         */
        isSingle: function () {
            return this.elems.getLength() === 1;
        },

        /**
         * Defines if group has multiple elements.
         * @return {Boolean}
         */
        isMultiple: function () {
            return this.elems.getLength() > 1;
        },

        /**
         * Returns an array of child components previews.
         *
         * @returns {Array}
         */
        getPreview: function () {
            return this.elems.map('getPreview');
        }
    });
});
