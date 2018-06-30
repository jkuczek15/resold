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
    'Magento_Ui/js/modal/modal-component',
    'uiRegistry',
    'underscore'
], function (Modal, registry, _) {
    'use strict';

    return Modal.extend({
        defaults: {
            stepWizard: '',
            modules: {
                form: '${ $.formName }'
            }
        },

        /**
         * Open modal
         */
        openModal: function () {
            var stepWizard = {};

            this.form().validate();

            if (this.form().source.get('params.invalid') === false) {
                stepWizard = registry.get('index = ' + this.stepWizard);

                if (!_.isUndefined(stepWizard)) {
                    stepWizard.open();
                }

                this._super();
            }
        }
    });
});
