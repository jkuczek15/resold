<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\CsMarketplace\Controller\Vendor;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Map extends \Ced\CsMarketplace\Controller\Vendor
{
    
    public $resultJsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }

    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $json = [];
        $customerId = $this->_getSession()->getVendorId();
        $reportHelper = $this->_objectManager->get('\Ced\CsMarketplace\Helper\Report');

        $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($customerId);
        if ($vendor && $vendor->getId()) {
            $results = $reportHelper->getTotalOrdersByCountry($vendor);
            
            foreach ($results as $country => $result) {
                $json[strtolower($country)] = [
                    'total'  => (string)$result['total'],
                    'amount' => (string)
                    $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')
                        ->format($result['amount'], false, 2, null, $this->_objectManager
                            ->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getBaseCurrencyCode())
                    ,
                ];
            }
        }
        return $resultJson->setData($json);
    }
}
