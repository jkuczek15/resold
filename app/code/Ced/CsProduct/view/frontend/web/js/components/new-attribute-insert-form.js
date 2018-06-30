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
    'Magento_Ui/js/form/components/insert-form'
], function (InsertForm) {
    'use strict';

    return InsertForm.extend({
        defaults: {
            modules: {
                productForm: 'product_form.product_form'
            },
            listens: {
                responseStatus: 'processResponseStatus'
            },
            attributeSetId: 0,
            productId: 0
        },

        /**
         * Process response status.
         */
        processResponseStatus: function () {
            if (this.responseStatus()) {

                if (this.productForm().params === undefined) {
                    this.productForm().params = {
                        set: this.attributeSetId
                    };
                }

                if (this.productId) {
                    this.productForm().params.id = this.productId;
                }
                this.productForm().params.type = this.productType;

                this.productForm().reload();
                this.resetForm();
            }
        }
    });
});
