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
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Session\Config\ConfigInterface;
use Plumrocket\GDPR\Helper\Data as DataHelper;
use Plumrocket\GDPR\Helper\Checkboxes as CheckboxesHelper;

/**
 * Class BaseCustomerObserver
 */
abstract class AbstractCustomerObserver implements ObserverInterface
{
    /**
     * @var DataHelper
     */
    public $dataHelper;

    /**
     * @var CheckboxesHelper
     */
    public $checkboxesHelper;

    /**
     * @var MessageManagerInterface
     */
    public $messageManager;

    /**
     * @var CookieManagerInterface
     */
    public $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var ConfigInterface
     */
    private $sessionConfig;

    /**
     * AbstractCustomerObserver constructor.
     * @param DataHelper $dataHelper
     * @param CheckboxesHelper $checkboxesHelper
     * @param MessageManagerInterface $messageManager
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ConfigInterface $sessionConfig
     */
    public function __construct(
        DataHelper $dataHelper,
        CheckboxesHelper $checkboxesHelper,
        MessageManagerInterface $messageManager,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        ConfigInterface $sessionConfig
    ) {
        $this->dataHelper = $dataHelper;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->messageManager = $messageManager;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionConfig = $sessionConfig;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    abstract public function execute(Observer $observer);

    /**
     * @param $customer
     * @return AbstractCustomerObserver
     */
    public function logCookiesConsents($customer)
    {
        return $this->logAllowCookiesConsent($customer)
            ->logDeclineCookiesConsent($customer);
    }

    /**
     * @param $customer
     * @return AbstractCustomerObserver
     */
    public function logAllowCookiesConsent($customer)
    {
        return $this->logCookiesConsent(
            $customer,
            \Magento\Cookie\Helper\Cookie::IS_USER_ALLOWED_SAVE_COOKIE,
            DataHelper::LAST_ALLOW_COOKIE_NAME
        );
    }

    /**
     * @param $customer
     * @return AbstractCustomerObserver
     */
    public function logDeclineCookiesConsent($customer)
    {
        return $this->logCookiesConsent(
            $customer,
            DataHelper::IS_USER_DECLINE_SAVE_COOKIE,
            DataHelper::LAST_DECLINE_COOKIE_NAME
        );
    }

    /**
     * @param $customer
     * @param $logAction
     * @param $cookieName
     * @return $this
     */
    private function logCookiesConsent($customer, $logAction, $cookieName)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        if ($customer && $customer->getId()) {
            $cookieValue = $this->cookieManager->getCookie($cookieName);

            if (! empty($cookieValue)) {
                $this->cookieManager->deleteCookie($cookieName, $this->getCookieMetadata());
                // Customer ID must be specified from here
                $this->checkboxesHelper->saveCookiesConsent(
                    $logAction,
                    $this->getCookiesConsentParams($customer->getId(), $cookieValue)
                );
            }
        }

        return $this;
    }

    /**
     * @param $customerId
     * @param $cookieValue
     * @return array
     */
    private function getCookiesConsentParams($customerId, $cookieValue)
    {
        $dateTime = null;
        $value = json_decode($cookieValue, true);
        $params = [
            'customer_id' => $customerId,
        ];

        if (is_array($value)) {
            if (! empty($value['label'])) {
                $params['label'] = (string)$value['label'];
            }

            if (! empty($value['datetime'])) {
                $dateTime = (int)$value['datetime'];
            }
        } else {
            $dateTime = (int)$value;
        }

        $params['created_at'] = $this->checkboxesHelper->getFormattedGmtDateTime($dateTime);

        return $params;
    }

    /**
     * @return \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    private function getCookieMetadata()
    {
        return $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath($this->sessionConfig->getCookiePath())
            ->setDomain($this->sessionConfig->getCookieDomain())
            ->setSecure($this->sessionConfig->getCookieSecure())
            ->setHttpOnly($this->sessionConfig->getCookieHttpOnly());
    }
}
