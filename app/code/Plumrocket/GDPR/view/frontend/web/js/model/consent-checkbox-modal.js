/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Plumrocket_GDPR/js/model/fancybox-fix'
], function ($, modal, $t, fancyBoxFix) {
    'use strict';

    return {
        modalWindow: {},
        self: this,

        /**
         * Create popUp window for provided element.
         *
         * @param {HTMLElement} element
         */
        createModal: function (element, consent, checkboxId) {
            var options = {
                'type': 'popup',
                'modalClass': 'prgdpr-consent-checkbox-modal',
                'responsive': true,
                'innerScroll': true,
                'title': consent.cms_page.title,
                'buttons': [
                    {
                        text: $t('I agree'),
                        class: 'action iagree primary',

                        /** @inheritdoc */
                        click: function () {
                            $('#'+checkboxId).prop('checked', true);
                            this.closeModal();
                        }
                    }
                ],
                opened: function($Event) {
                    $('header.modal-header', $Event.currentTarget).append($('.modal-content .prgdpr-consent-content-actions', $Event.currentTarget));
                    fancyBoxFix.hideFancyBox();
                },
                closed: function($Event) {
                    fancyBoxFix.showFancyBox();
                }
            };
            this.modalWindow[checkboxId] = modal(options, $(element));
        },

        /** Show popup window */
        showModal: function (id) {
            this.modalWindow[id].openModal();
        }
    };
});