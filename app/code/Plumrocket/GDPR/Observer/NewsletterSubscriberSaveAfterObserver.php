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

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * Class NewsletterSubscriberSaveAfterObserver
 */
class NewsletterSubscriberSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * ValidateConsentsObserver constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper,
        \Plumrocket\GDPR\Helper\Data $dataHelper
    ) {
        $this->request = $request;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var null|\Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $observer->getData('data_object');

        if ($subscriber && $this->dataHelper->moduleEnabled()) {
            $this->saveSubscriberConsents($subscriber);
        }
    }

    /**
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return $this
     */
    private function saveSubscriberConsents($subscriber)
    {
        if ($subscriber && $subscriber->isSubscribed()) {
            $this->checkboxesHelper->saveMultipleConsents(
                ConsentLocations::NEWSLETTER,
                $this->request->getParam('consent')
            );
        }

        return $this;
    }
}
