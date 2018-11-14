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

class RemovalStatus implements \Magento\Framework\Option\ArrayInterface
{
    const PENDING = 'pending';
    const CANCELLED = 'cancelled';
    const COMPLETED = 'completed';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PENDING,
                'label' => __('Pending'),
            ],
            [
                'value' => self::CANCELLED,
                'label' => __('Cancelled'),
            ],
            [
                'value' => self::COMPLETED,
                'label' => __('Completed'),
            ],
        ];
    }
}
