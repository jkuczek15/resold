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
    'mage/storage',
    'mage/translate',
    'Plumrocket_GDPR/js/model/fancybox-fix'
], function ($, modal, storage, $t, fancyBoxFix) {
    'use strict';

    return {
        /**
         * Create popUp window for provided element.
         *
         * @param {HTMLElement} element
         */
        createModal: function (element, consent) {
            var title = (consent.cms_page) ? consent.cms_page.title : null;

            var options = {
                'type': 'popup',
                'modalClass': 'prgdpr-consent-popup-modal',
                'responsive': true,
                'innerScroll': true,
                'title': title,
                'buttons': [
                    {
                        text: $t('I agree'),
                        class: 'action iagree primary',

                        /** @inheritdoc */
                        click: function () {
                            storage.post(consent.agreeUrl, JSON.stringify({'consent': consent}), true);
                            this.closeModal();
                        }
                    }
                ],
                opened: function($Event) {
                    $('.modal-header button.action-close', $Event.currentTarget).remove();
                    $('header.modal-header', $Event.currentTarget).append($('.prgdpr-consent-content-actions', $Event.currentTarget));
                    fancyBoxFix.hideFancyBox();
                },
                closed: function($Event) {
                    fancyBoxFix.showFancyBox();
                },
                keyEventHandlers: {
                    escapeKey: function () { return; }
                }
            };
            modal(options, $(element)).openModal();
        }
    };
});