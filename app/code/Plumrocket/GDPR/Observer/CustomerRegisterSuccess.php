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
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Plumrocket\GDPR\Helper\Data as DataHelper;
use Plumrocket\GDPR\Observer\AbstractCustomerObserver;

/**
 * Class CustomerRegisterSuccess
 */
class CustomerRegisterSuccess extends AbstractCustomerObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getData('customer');
        /** @var \Magento\Customer\Controller\Account\CreatePost $controller */
        $controller = $observer->getData('account_controller');

        if ($this->dataHelper->moduleEnabled()) {
            $this->logConsentCheckboxes(
                $customer,
                $controller->getRequest()->getParam('consent', null)
            );
            $this->logCookiesConsents($customer);
        }
    }

    /**
     * @param $customer
     * @param $consents
     * @return $this
     */
    public function logConsentCheckboxes($customer, $consents)
    {
        if ($customer && $customer->getId()) {
            $this->checkboxesHelper->saveMultipleConsents(
                ConsentLocations::REGISTRATION,
                $consents,
                [
                    'customer_id' => $customer->getId(),
                ]
            );
        }

        return $this;
    }
}
