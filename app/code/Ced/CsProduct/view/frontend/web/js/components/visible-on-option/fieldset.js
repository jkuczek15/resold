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
    'Magento_Ui/js/form/components/fieldset',
    'Magento_Catalog/js/components/visible-on-option/strategy'
], function (Fieldset, strategy) {
    'use strict';

    return Fieldset.extend(strategy).extend(
        {
            defaults: {
                openOnShow: true
            },

            /**
             * Toggle visibility state.
             */
            toggleVisibility: function () {
                this._super();

                if (this.openOnShow) {
                    this.opened(this.inverseVisibility ? !this.isShown : this.isShown);
                }
            }
        }
    );
});
