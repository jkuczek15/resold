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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block;

use Plumrocket\GDPR\Helper\CustomerData;

class GDPR extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CustomerData
     */
    private $customerData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context          $context
     * @param CustomerData                                              $customerData
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerData $customerData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerData = $customerData;
    }
}
