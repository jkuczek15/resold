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
    'uiComponent',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal'
], function ($, Component, __, alert, modal) {
    'use strict';

    window.revisionHistoryModalManager = {
        baseActionUrl: false,
        containerIdPrefix: 'revision-history-view-modal-',
        containerClass: 'revision-history-view-modal',

        /**
         * Close all modals
         */
        closeAllModals: function () {
            /* Close all modals */
            $('.' + this.containerClass).each(function () {
                $(this).modal('closeModal');
            });

            return this;
        },

        /**
         * Open modal
         */
        openModal: function (historyId) {
            historyId = parseInt(historyId);
            this.closeAllModals();
            $('#' + this.containerIdPrefix + historyId).modal('openModal');

            return this;
        },

        /**
         * Load modal data
         *
         * @param {string} message - message text.
         */
        showMessage: function (message) {
            this.closeAllModals();
            alert({
                title: __('Error loading revision history.'),
                content: message
            });

            return this;
        },

        /**
         * Load modal data
         *
         * @param {int} historyId - request history identifier.
         * @param {boolean} forceOpen  - force open modal window flag.
         */
        loadModal: function(historyId, forceOpen = false) {
            historyId = parseInt(historyId);
            var managerObject = this;

            if (! this.baseActionUrl) {
                this.showMessage(__('Component source URL not specified.'));
                return false;
            }

            $.ajax({
                url: managerObject.baseActionUrl,
                data: {
                    history_id: historyId
                },
                method: 'GET',
                showLoader: true,
                dataType: 'json',
                success: function(response) {
                    if (! response.success) {
                        managerObject.showMessage(response.messages.join('<br />'));
                        return false;
                    }

                    if (! response.data) {
                        managerObject.showMessage(__('Invalid data was returning.'));
                        return false;
                    }

                    var versionSubTitle = __('Version: %1').replace('%1', response.data.version);
                    var authorSubTitle = __('Updated By: %1').replace('%1', response.data.user_name);

                    var modalOptions = {
                        title: __('Revision %1').replace('%1', response.data.version),
                        subTitle: '(' + authorSubTitle + ')',
                        type: 'popup',
                        buttons: [
                            {
                                text: __('Close'),
                                class: 'action secondary action-hide-popup',

                                /** @inheritdoc */
                                click: function () {
                                    this.closeModal();
                                }
                            }
                        ]
                    };

                    var modalContainer = $('<div class="' + managerObject.containerClass + '" id="'+ managerObject.containerIdPrefix + historyId + '"/>');
                    var beforeText = '<h4 style="border-bottom:solid 1px #000"></h4>';
                    modalContainer.html(beforeText + response.data.content);
                    modalContainer.modal(modalOptions);

                    if (forceOpen) {
                        managerObject.openModal(historyId);
                    }
                }
            });

            return this;
        },

        /**
         * Load modal data
         *
         * @param {int} historyId - request history identifier.
         */
        showModal: function (historyId) {
            historyId = parseInt(historyId);

            if (historyId > 0) {
                if (! $('#' + this.containerIdPrefix + historyId).length) {
                    this.loadModal(historyId, true);
                } else {
                    this.openModal(historyId);
                }
            } else {
                this.showMessage(__('Revision history ID not specified.'));
            }

            return this;
        }
    };

    return function (config) {
        window.revisionHistoryModalManager.baseActionUrl = config.baseActionUrl;
        window.revisionHistoryModalManager.containerIdPrefix = config.containerIdPrefix;
        window.revisionHistoryModalManager.containerClass = config.containerClass;
    };
});