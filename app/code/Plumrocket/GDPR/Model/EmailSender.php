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

namespace Plumrocket\GDPR\Model;

use Magento\Framework\App\Area;

class EmailSender extends \Magento\Framework\DataObject
{
    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * EmailSender constructor.
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        parent::__construct($data);
    }

    /**
     * Retrieve config value
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->dataHelper->getEmailSenderName();
    }

    /**
     * Retrieve config value
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->dataHelper->getEmailSenderEmail();
    }

    /**
     * @param $template
     * @param null|array $vars
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     */
    private function getPreparedTransportBuilder($template, $vars = null)
    {
        if (empty($vars) || ! is_array($vars)) {
            $vars = [];
        }

        return $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions([
            'area' => Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId(),
        ])->setTemplateVars(
            $vars
        )->setFrom([
            'email' => $this->getSenderEmail(),
            'name' => $this->getSenderName(),
        ]);
    }

    /**
     * @param $template
     * @param $toEmail
     * @param null $toName
     * @param null $vars
     * @return bool
     */
    private function sendNotification($template, $toEmail, $toName = null, $vars = null)
    {
        try {
            $toName = ! empty($toName) ? (string)$toName : 'Recipient Name';

            if (empty($toEmail)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid specified recipient email address.')
                );
            }

            /* Send email */
            $this->getPreparedTransportBuilder($template, $vars)
                ->addTo($toEmail, $toName)
                ->getTransport()
                ->sendMessage();

            return true;
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->logger->critical($e->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return false;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkCustomerBeforeSendNotification(\Magento\Customer\Model\Customer $customer)
    {
        if (! $customer->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid customer.'));
        }

        return $this;
    }

    /**
     * Send transactional email about downloading customer data from account
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param null|array $vars
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendDownloadDataNotification(\Magento\Customer\Model\Customer $customer, $vars = null)
    {
        $this->checkCustomerBeforeSendNotification($customer);
        /** @var string $template */
        $template = $this->dataHelper->getEmailDownloadConfirmationTemplate();

        return $this->sendNotification($template, $customer->getEmail(), $customer->getName(), $vars);
    }

    /**
     * Send transactional email about scheduled to remove customer account
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param null|array $vars
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendRemovalRequestNotification(\Magento\Customer\Model\Customer $customer, $vars = null)
    {
        $this->checkCustomerBeforeSendNotification($customer);
        /** @var string $template */
        $template = $this->dataHelper->getEmailRemovalRequestTemplate();

        return $this->sendNotification($template, $customer->getEmail(), $customer->getName(), $vars);
    }
}
