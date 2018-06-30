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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */  

namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor;

class CheckAvailability extends \Magento\Backend\App\Action
{
protected $resultJsonFactory;
public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }
    /**
     * Customers list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        $id = $this->getRequest()->getParam('vendor_id', 0);
        $venderData = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParam('vendor', array());
        $array = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->checkAvailability($venderData, $id);
       if(!$array ['success']){
           $response->setError(true); 
       $response->setMessage(
          __($array ['message'].$array ['suggestion'])
       );
        }
        
       return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}