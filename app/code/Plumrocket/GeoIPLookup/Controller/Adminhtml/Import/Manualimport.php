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

namespace Plumrocket\GeoIPLookup\Controller\Adminhtml\Import;

class Manualimport extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip
     */
    private $maxmindgeoip;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry
     */
    private $iptocountry;

    /**
     * Manualimport constructor.
     *
     * @param \Magento\Backend\App\Action\Context                    $context
     * @param \Magento\Framework\Controller\Result\JsonFactory       $resultJsonFactory
     * @param \Magento\Framework\Json\Helper\Data                    $jsonHelper
     * @param \Plumrocket\GeoIPLookup\Helper\Data                    $dataHelper
     * @param \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindgeoip
     * @param \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry  $iptocountry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Plumrocket\GeoIPLookup\Helper\Data $dataHelper,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindgeoip,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry $iptocountry
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->dataHelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;
        $this->maxmindgeoip = $maxmindgeoip;
        $this->iptocountry = $iptocountry;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json
     * |\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        session_write_close();
        $resultJson = $this->resultJsonFactory->create();
        $dataId = $this->getRequest()->getParam('dataId');
        $modelName = $this->dataHelper->getModelNameByElementId($dataId, false);
        $importModel = $this->{$modelName};

        $result = $importModel->manualImportData();

        return $resultJson->setData(
            $this->jsonHelper->jsonEncode($result)
        );
    }
}