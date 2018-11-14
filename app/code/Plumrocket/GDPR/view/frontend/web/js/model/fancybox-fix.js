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
    'jquery'
], function ($) {
    'use strict';

    return {
        /**
         * Get fancybox jQuery element
         *
         * @return {*|jQuery.fn.init|m.fn.init|n.fn.init|jQuery|HTMLElement}
         */
        getFancyBoxEl: function () {
            return $('.fancybox-wrap');
        },

        /**
         * Validate element
         *
         * @param el
         * @return {*}
         */
        isValidEl: function (el) {
            return el && el.length;
        },

        /**
         * Set visibility for fancybox and overlay jQuery elements
         *
         * @param show
         */
        setFancyBoxVisibility: function (show) {
            var fancyBox = this.getFancyBoxEl();

            if (this.isValidEl(fancyBox)) {
                fancyBox.toggle(show);
                fancyBox.next('.fancybox-overlay').toggle(show);
            }
        },

        /**
         * Hide fancybox and overlay jQuery elements
         */
        hideFancyBox: function () {
            this.setFancyBoxVisibility(false);
        },

        /**
         * Show fancybox and overlay jQuery elements
         */
        showFancyBox: function () {
            this.setFancyBoxVisibility(true);
        }
    };
});