<?php
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

namespace Plumrocket\GDPR\Model\Config\Source;

class ConsentLocations implements \Magento\Framework\Option\ArrayInterface
{
    const REGISTRATION  = 'registration';
    const CHECKOUT      = 'checkout';
    const NEWSLETTER    = 'newsletter';
    const POPUP_NOTIFY  = 'popup_notify';
    const COOKIE        = 'cookie';
    const CONTACT_US    = 'contact_us';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::REGISTRATION,
                'label' => __('Registration Page'),
            ],
            [
                'value' => self::CHECKOUT,
                'label' => __('Checkout Page'),
            ],
            [
                'value' => self::NEWSLETTER,
                'label' => __('Newsletter Subscription'),
            ],
            [
                'value' => self::POPUP_NOTIFY,
                'label' => __('Popup Notification'),
            ],
            [
                'value' => self::COOKIE,
                'label' => __('Cookie Consent Notice'),
            ],
            [
                'value' => self::CONTACT_US,
                'label' => __('Contact Us Page'),
            ],
        ];
    }

    /**
     * Return array of options
     *
     * @param bool $forSelect
     * @return array Format: array('value' => '<label>', ...)
     */
    public function toOptionAssocArray($forSelect = false)
    {
        $options = $this->toOptionArray();
        $assocOptions = [];

        foreach ($options as $option) {
            if ($forSelect
                && ! $this->canShowInSelect($option['value'])
            ) {
                continue;
            }

            $assocOptions[$option['value']] = $option['label'];
        }

        return $assocOptions;
    }

    /**
     * @param $value
     * @return bool
     */
    private function canShowInSelect($value)
    {
        return ! in_array((string)$value, [
            self::POPUP_NOTIFY,
            self::COOKIE,
        ]);
    }
}
