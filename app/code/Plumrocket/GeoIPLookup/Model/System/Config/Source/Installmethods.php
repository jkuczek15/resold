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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GeoIPLookup\Model\System\Config\Source;

class Installmethods implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Apply automatic installation
     */
    const AUTOMATIC = 'automatic';

    /**
     * Apply manual installation
     */
    const MANUAL = 'manual';

    /**
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::AUTOMATIC,
                'label' => __('Automatic Installation'),
            ],
            [
                'value' => self::MANUAL,
                'label' => __('Manual Installation'),
            ],
        ];
    }
}
