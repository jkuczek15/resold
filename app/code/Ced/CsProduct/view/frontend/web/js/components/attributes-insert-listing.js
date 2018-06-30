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
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/form/components/insert-listing'
], function ($, registry, _, InsertListing) {
    'use strict';

    return InsertListing.extend({
        defaults: {
            addAttributeUrl: '',
            attributeSetId: '',
            attributeIds: '',
            groupCode: '',
            groupName: '',
            groupSortOrder: 0,
            productId: 0,
            formProvider: '',
            modules: {
                form: '${ $.formProvider }',
                modal: '${ $.parentName }'
            },
            productType: ''
        },

        /**
         * Render attribute
         */
        render: function () {
            this._super();
        },

        /**
         * Save attribute
         */
        save: function () {
            this.addSelectedAttributes();
            this._super();
        },

        /**
         * Add selected attributes
         */
        addSelectedAttributes: function () {
            $.ajax({
                url: this.addAttributeUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    attributeIds: this.selections().getSelections(),
                    templateId: this.attributeSetId,
                    groupCode: this.groupCode,
                    groupName: this.groupName,
                    groupSortOrder: this.groupSortOrder,
                    productId: this.productId,
                    componentJson: 1
                },
                success: function () {
                    this.form().params = {
                        set: this.attributeSetId,
                        id: this.productId,
                        type: this.productType
                    };
                    this.form().reload();
                    this.modal().state(false);
                    this.reload();
                }.bind(this)
            });
        }
    });
});
