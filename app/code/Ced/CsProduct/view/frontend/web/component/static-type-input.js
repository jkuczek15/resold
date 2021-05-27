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
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            parentOption: null
        },

        /**
         * Initialize component.
         *
         * @returns {Element}
         */
        initialize: function () {
            return this
                ._super()
                .initLinkToParent();
        },

        /**
         * Cache link to parent component - option holder.
         *
         * @returns {Element}
         */
        initLinkToParent: function () {
            var pathToParent = this.parentName.replace(/(\.[^.]*){2}$/, '');

            this.parentOption = registry.async(pathToParent);
            this.value() && this.parentOption('label', this.value());

            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.parentOption(function (component) {
                component.set('label', value ? value : component.get('headerLabel'));
            });

            return this._super();
        }
    });
});
