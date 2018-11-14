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

namespace Plumrocket\GeoIPLookup\Controller\Adminhtml\Test;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\GeoIPLookup
     */
    private $geoIPLookup;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Json\Helper\Data              $jsonHelper
     * @param \Plumrocket\GeoIPLookup\Model\GeoIPLookup        $geoIPLookup
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Plumrocket\GeoIPLookup\Model\GeoIPLookup $geoIPLookup
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonHelper = $jsonHelper;
        $this->geoIPLookup = $geoIPLookup;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json
     * |\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $ip = $this->getRequest()->getParam('ip');
        $result = $this->geoIPLookup->geoIpLookupTest($ip);

        return $resultJson->setData(
            $this->jsonHelper->jsonEncode($result)
        );
    }
}