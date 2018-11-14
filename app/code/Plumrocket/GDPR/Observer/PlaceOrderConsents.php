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

namespace Plumrocket\GDPR\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Helper\Data as DataHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Plumrocket\GDPR\Plugin\Magento\Checkout\AbstractValidation;

/**
 * Class PlaceOrderConsents
 * @package Plumrocket\GDPR\Observer
 */
class PlaceOrderConsents implements ObserverInterface
{
    /**
     * @var Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * PlaceOrderConsents constructor.
     * @param Checkboxes $checkboxesHelper
     * @param DataHelper $dataHelper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Checkboxes $checkboxesHelper,
        DataHelper $dataHelper,
        CheckoutSession $checkoutSession
    ) {
        $this->checkboxesHelper = $checkboxesHelper;
        $this->dataHelper = $dataHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');

        if ($order && $this->dataHelper->moduleEnabled()) {
            $this->saveOrderConsents($order);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    private function saveOrderConsents($order)
    {
        if ($customerId = $order->getCustomerId()) {
            $consents = $this->checkoutSession->getStepData(AbstractValidation::DEFAULT_STEP_NAME);
            $forceData = [
                'customer_id' => $customerId,
                'website_id' => $order->getStore()->getWebsiteId(),
            ];

            $this->checkboxesHelper->saveMultipleConsents(ConsentLocations::CHECKOUT, $consents, $forceData);
            $this->checkoutSession->setStepData(AbstractValidation::DEFAULT_STEP_NAME, []);
        }

        return $this;
    }
}
