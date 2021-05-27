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
    'jquery',
    'Magento_Catalog/catalog/type-events'
], function ($, productType) {
    'use strict';

    return {
        $initiallyDisabledAttributes: [],
        $links: $('[data-ui-id=product-tabs-tab-link-advanced-pricing]'),
        $tab: $('[data-tab-panel=advanced-pricing]'),
        toggleDisabledAttribute: function (disabled) {
            $('input,select', this.$tab).each(function (index, element) {
                if (!$.inArray(element, this.$initiallyDisabledAttributes)) {
                    $(element).attr('disabled', disabled);
                }
            });
        },
        init: function () {
            $(document).on('changeTypeProduct', this._initType.bind(this));
            this._setInitialState();
            this._initType();
        },
        _setInitialState: function () {
            if (this.$initiallyDisabledAttributes.length == 0) {
                this.$initiallyDisabledAttributes = $('input:disabled,select:disabled', this.$tab).toArray();
            }
        },
        _initType: function () {
            var isConfigurable = productType.type.current === 'configurable';

            if (isConfigurable) {
                this.$links.hide();
            } else {
                this.$links.show();
            }

            this.toggleDisabledAttribute(isConfigurable);
        }
    };
});
