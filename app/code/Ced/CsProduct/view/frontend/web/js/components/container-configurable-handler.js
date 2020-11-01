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
    'uiComponent'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            listens: {
                '${ $.provider }:data.is_downloadable': 'handleProductType'
            },
            links: {
                isDownloadable: '${ $.provider }:data.is_downloadable'
            },
            modules: {
                createConfigurableButton: '${$.createConfigurableButton}'
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            this.handleProductType(this.isDownloadable);

            return this;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['content']);

            return this;
        },

        /**
         * Change content for container and visibility for button
         *
         * @param {String} isDownloadable
         */
        handleProductType: function (isDownloadable) {
            if (isDownloadable === '1') {
                this.content(this.content2);

                if (this.createConfigurableButton()) {
                    this.createConfigurableButton().visible(false);
                }
            } else {
                this.content(this.content1);

                if (this.createConfigurableButton()) {
                    this.createConfigurableButton().visible(true);
                }
            }
        }
    });
});
