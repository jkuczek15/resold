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

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Plumrocket_GDPR/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Plumrocket_GDPR/js/model/set-payment-information-mixin': true
            },
            'Amazon_Payment/js/action/place-order': {
                'Plumrocket_GDPR/js/model/place-order-mixin': true
            }
        }
    }
};