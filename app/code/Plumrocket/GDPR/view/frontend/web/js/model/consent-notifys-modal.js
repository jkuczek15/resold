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
    'mage/translate'
], function ($, modal, storage, $t) {
    'use strict';

    return {
        
        /**
         * Create popUp window for provided element.
         *
         * @param {HTMLElement} element
         */
        createModal: function (element, consent) {

            var remindLater = true;

            var options = {
                'type': 'popup',
                'modalClass': 'prgdpr-consent-notifys-modal',
                'responsive': true,
                'innerScroll': true,
                'title': null,
                'buttons': [
                    {
                        text: $t('Remind me later'),
                        class: 'action remind-later secondary',

                        /** @inheritdoc */
                        click: function () {
                            this.closeModal();
                        }
                    },
                    {
                        text: $t('I agree'),
                        class: 'action iagree primary',

                        /** @inheritdoc */
                        click: function () {
                            remindLater = false;
                            storage.post(consent.agreeUrl, JSON.stringify({'consent': consent}), true);
                            this.closeModal();
                        }
                    }
                ],
                opened: function($Event) {
                    $('header.modal-header', $Event.currentTarget).append($('.prgdpr-consent-notifys-content', $Event.currentTarget));
                    $('header.modal-header', $Event.currentTarget).append($('.prgdpr-consent-content-actions', $Event.currentTarget));
                },
                closed: function() {
                    if (remindLater) {
                        storage.post(consent.remindLaterUrl, JSON.stringify({'consent': consent}), true);
                    }
                }
            };
            modal(options, $(element)).openModal();
        }

    };
});