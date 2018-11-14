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
    'jquery/ui',
    'mage/cookies'
], function ($) {
    'use strict';

    $.widget('mage.cookieNotices', {
        /** @inheritdoc */
        _create: function () {
            var cookieExpires = this._getCookieExpires(),
                allowedCookieName = this.options.allowedCookieName,
                declinedCookieName = this.options.declinedCookieName;

            var canShowElement = ! $.mage.cookies.get(this.options.cookieNameDecline)
                ? ! $.mage.cookies.get(this.options.cookieName)
                : false;

            this.element.toggle(canShowElement);

            $(this.options.cookieAllowButtonSelector).on('click', $.proxy(function () {
                $.mage.cookies.set(this.options.cookieName, JSON.stringify(this.options.cookieValue), {
                    expires: cookieExpires
                });

                if ($.mage.cookies.get(this.options.cookieName)) {
                    this._sendCookieConsent(this.options.cookieName, allowedCookieName);
                } else {
                    window.location.href = this.options.noCookiesUrl;
                }
            }, this));

            $(this.options.cookieDeclineButtonSelector).on('click', $.proxy(function () {
                $.mage.cookies.set(this.options.cookieNameDecline, JSON.stringify(this.options.cookieValue), {
                    expires: cookieExpires
                });

                if ($.mage.cookies.get(this.options.cookieNameDecline)) {
                    $.mage.cookies.clear(this.options.cookieName);
                    $.mage.cookies.clear(allowedCookieName);
                    this._sendCookieConsent(this.options.cookieNameDecline, declinedCookieName);
                } else {
                    window.location.href = this.options.noCookiesUrl;
                }
            }, this));
        },
        _getCookieExpires: function () {
            return new Date(new Date().getTime() + this.options.cookieLifetime * 1000);
        },
        _sendCookieConsent: function (logActionName, timestampCookieName) {
            var cookieExpires = this._getCookieExpires();

            $.ajax({
                url: this.options.logActionForAllowCookies,
                data: {
                    global: false,
                    log_action: logActionName
                },
                method: 'GET',
                showLoader: true,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var value = JSON.stringify({
                            label: response.label,
                            datetime: response.datetime
                        });

                        if (! response.logged) {
                            $.mage.cookies.set(timestampCookieName, value, {
                                expires: cookieExpires
                            });
                        }

                        window.location.reload();
                    }
                }
            });
        }
    });

    return $.mage.cookieNotices;
});