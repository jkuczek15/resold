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

require([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'domReady!'
], function ($, confirm, __) {
    'use strict';

    var saveAndContinueButton = $('#save_and_continue'),
        gdprSaveAndContinueButton = $('#gdpr_save_and_continue'),
        saveButton = $('#save'),
        gdprSaveButton = $('#gdpr_save');

    saveAndContinueButton.hide();
    saveButton.hide();
    gdprSaveAndContinueButton.show();
    gdprSaveButton.show();

    window.confirmSaveCmsPage = function (nextAction) {
        if (isRevisionEnabled()
            && isContentChanged()
            && ! isVersionChanged()
        ) {
            showConfirmationModal(nextAction);
        } else {
            proceedNextAction(nextAction);
        }
    };

    function isRevisionEnabled() {
        var initialEnableElement = $('input[name="revision[original_enable_revisions]"]'),
            enableElement = $('input[name="revision[enable_revisions]"]');

        return enableElement.length
            ? (1 === parseInt(enableElement.val()))
            : (initialEnableElement.length && (1 === parseInt(initialEnableElement.val())));
    }

    function isVersionChanged() {
        var initialVersionElement = $('input[name="revision[original_document_version]"]'),
            versionElement = $('input[name="revision[document_version]"]');

        return versionElement.length
            ? (versionElement.val() !== initialVersionElement.val())
            : false;
    }

    function isContentChanged() {
        var initialContentElement = $('textarea[name="revision[original_content]"]'),
            contentElement = $('textarea[name="content"]');

        return (contentElement.length && initialContentElement.length)
            ? ($(contentElement.val().replace(/\s/g, '')).text() !== $(initialContentElement.val().replace(/\s/g, '')).text())
            : false;
    }

    function showConfirmationModal(nextAction) {
        var docHref = 'http://wiki.plumrocket.com/Magento_2_GDPR_v1.x_Configuration#Configuring_GDPR_Magento_2_for_CMS_Pages',
            docText = __('%1Read here%2 on why do you want to use the document versions.');

        docText = docText.replace('%1', '<a target="_blank" href="' + docHref + '">').replace('%2', '</a>');

        var content = '<h3 style="border-bottom: solid 1px #000">' + __('Update Confirmation') + '</h3>'
            + '<span class="page-title">' + __('Important!') + '</span><br/><br/>'
            + '<p>' + __('You have updated the page content, but the document version hasn\'t changed. Do you want to proceed anyway?') + '</p><br/>'
            + '<p>' + __('Open "GDPR Settings" Tab in order to change the "Document Version".') + '</p><br/>'
            + '<p>' + docText + '</p>';

        confirm({
            title: '',
            content: content,
            focus: '.action-dismiss',
            actions: {
                /**
                 * Callback confirm.
                 */
                confirm: function () {},

                /**
                 * Callback cancel.
                 */
                cancel: function () {}
            },
            buttons: [{
                text: $.mage.__('Cancel'),
                class: 'action-secondary action-dismiss',

                /**
                 * Click handler.
                 */
                click: function () {
                    this.closeModal();
                    var settingsFieldset = $('div[data-index="prgdpr_settings"]').find('.fieldset-wrapper-title');

                    if (settingsFieldset.length) {
                        if (settingsFieldset.attr('data-state-collapsible') !== 'open') {
                            settingsFieldset.trigger("click");
                        }

                        $('html, body').animate({
                            //scrollTop: $('input[name="revision[document_version]"]').offset().top
                            scrollTop: settingsFieldset.offset().top
                        }, 2000);
                    }
                }
            }, {
                text: $.mage.__('Yes, Save Changes'),
                class: 'action-primary action-accept',

                /**
                 * Click handler.
                 */
                click: function () {
                    this.closeModal(true);
                    proceedNextAction(nextAction);
                }
            }]
        });
    }

    function proceedNextAction(nextAction) {
        switch (nextAction) {
            case 'save':
                saveButton.trigger("click");
                break;

            case 'saveAndContinue':
                saveAndContinueButton.trigger("click");
                break;

            default:
                alert("Undefined Next Action");
        }
    }
});