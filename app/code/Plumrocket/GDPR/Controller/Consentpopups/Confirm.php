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

namespace Plumrocket\GDPR\Controller\Consentpopups;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Plumrocket\GDPR\Model\ConsentsLogFactory as ConsentsLogFactory;
use Plumrocket\GDPR\Model\ResourceModel\ConsentsLogFactory as ConsentsLogResourceFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Confirm action.
 */
class Confirm extends Action
{
    /**
     * @var ConsentsLogFactory
     */
    protected $consentsLogFactory;

    /**
     * @var ConsentsLogResourceFactory
     */
    protected $consentsLogResourceFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param ConsentsLogFactory $consentsLogFactory
     * @param ConsentsLogResourceFactory $consentsLogResourceFactory
     * @param Session $session
     * @param FilterManager $filterManager
     * @param ResultJsonFactory $resultJsonFactory
     * @param JsonHelper $jsonHelper
     * @param StoreManagerInterface $storeManager
     * @param RemoteAddress $remoteAddress
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        ConsentsLogFactory $consentsLogFactory,
        ConsentsLogResourceFactory $consentsLogResourceFactory,
        Session $session,
        FilterManager $filterManager,
        ResultJsonFactory $resultJsonFactory,
        JsonHelper $jsonHelper,
        StoreManagerInterface $storeManager,
        RemoteAddress $remoteAddress,
        DateTime $dateTime
    ) {
        parent::__construct($context);

        $this->consentsLogFactory = $consentsLogFactory;
        $this->consentsLogResourceFactory = $consentsLogResourceFactory;
        $this->session = $session;
        $this->filterManager = $filterManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        $consent = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        try {
            $requestData = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());

            if ($consent = $requestData['consent']) {
                $consentsLog = $this->consentsLogFactory->create()
                    ->setData([
                        'created_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                        'customer_id' => $this->session->getCustomerId(),
                        'website_id' => $this->storeManager->getStore()->getWebsiteId(),
                        'customer_ip' => $this->remoteAddress->getRemoteAddress(),
                        'location' => ConsentLocations::REGISTRATION,
                        'label' =>  $this->filterManager->stripTags($consent['checkbox_label']),
                        'cms_page_id' => $consent['page_id'],
                        'version' => ($consent['cms_page']) ? $consent['cms_page']['version'] : null
                    ]);
                $this->consentsLogResourceFactory->create()->save($consentsLog);
            }

            $response = ['error' => false];
        } catch (\Exception $e) {
            return $resultJson->setData([$e->getMessage()])->setHttpResponseCode($httpBadRequestCode);
        }

        return $resultJson->setData($response);
    }
}
