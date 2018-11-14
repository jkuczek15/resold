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

namespace Plumrocket\GDPR\Controller\Cookiesnotices;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * Load action.
 */
class Log extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Action\Context
     */
    private $context;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * Log constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
    ) {
        $this->context = $context;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataHelper = $dataHelper;
        $this->checkboxesHelper = $checkboxesHelper;
        parent::__construct($context);
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $response = [
            'success' => false,
            'message' => __('Not found.')
        ];

        if ($this->dataHelper->moduleEnabled()) {
            try {
                $logAction = strtolower($this->getRequest()->getParam('log_action'));
                $label = $this->checkboxesHelper->getCookiesConsentButtonLabel($logAction);
                $textLabel = $this->checkboxesHelper->getCookiesConsentActionValue($logAction)
                    ? 'accepted'
                    : 'declined';
                $response = [
                    'success' => true,
                    'message' => __('You %1 cookies from this site.', $textLabel),
                ];

                if ($this->checkboxesHelper->saveCookiesConsent($logAction, ['label' => $label])) {
                    $response['logged'] = true;
                } else {
                    $response['logged'] = false;
                    $response['datetime'] = $this->checkboxesHelper->getGmtTimestamp();
                    $response['label'] = $label;
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                $response = [
                    'success' => false,
                    'message' => __('Something went wrong.')
                ];
            }
        }

        return $resultJson->setData($response);
    }
}
