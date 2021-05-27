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
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

Class VendorRegistration implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request,
        ManagerInterface $messageManager
    ) {
    
        
        $this->_objectManager = $objectManager;
        $this->request = $request;
        $this->messageManager = $messageManager;
    }
    /**
     *Vendor registration 
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        if($this->request->getParam('is_vendor') == 1) {
            $venderData = $this->request->getParam('vendor');
            $customerData = $observer->getCustomer();
            try {
                $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')
                        ->setCustomer($customerData)
                        ->register($venderData);
                if(!$vendor->getErrors()) {
                    $vendor->save();
                    if($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS) {
                        $this->messageManager->addSuccess(__('Your vendor application has been Pending.'));
                    } else if ($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
                        $this->messageManager->addSuccess(__('Your vendor application has been Approved.'));
                    }
                } elseif ($vendor->getErrors()) {
                    foreach ($vendor->getErrors() as $error) {
                        $this->messageManager->addError($error);
                    }
                    $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getCustomerSession()->setFormData($venderData);
                } else {
                    $this->messageManager->addError($this->_objectManager->get('Ced\CsMarketplace\Helper\data')->__('Your vendor application has been denied'));
                }
            } catch (\Exception $e) {
                $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->logException($e);
            }
        }
    }
}    

