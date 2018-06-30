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

namespace Ced\CsMarketplace\Controller\Vshops;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Framework\App\Action\Action
{
    
    protected $_coreRegistry;
    
    protected $resultForwardFactory;
    
    protected $resultPageFactory;    
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
    
    protected function _initVendor()
    {    
        $this->_eventManager->dispatch('csmarketplace_controller_vshops_init_before', array('controller_action' => $this));
        
        if(!$this->_objectManager->get('Ced\CsMarketplace\Helper\Acl')->isEnabled()) {
            return false; 
        }
        
        $shopUrl = $this->getRequest()->getParam('shop_url');
        if (!strlen($shopUrl)) {
            return false;
        }
        $storeId = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStore()->getId();
        
        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')
                    ->setStoreId($storeId)->loadByAttribute('shop_url', $shopUrl);
        
        if (!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->canShow($vendor)) {
            return false;
        } else if(!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->isShopEnabled($vendor)) {
            return false;
        }
        $this->_coreRegistry->register('current_vendor', $vendor);
     
        try {
            $this->_eventManager->dispatch(
                'csmarketplace_controller_vshops_init_after',
                array(
                'vendor' => $vendor,
                'controller_action' => $this
                )
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Invalid login or password.'));
        } 
        return $vendor;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // product filter start
        $data = $this->getRequest()->getParams();
        if (isset($data['product_list_dir'])) { 
            $this->_coreRegistry->register('name_filter', $data['product_list_dir']);
        }
        // product filter end
        if ($vendor = $this->_initVendor()) {
            $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
            if ($this->_coreRegistry->registry('current_category') == null) {
                $category = $this->_objectManager->create('Magento\Catalog\Model\Category')
                            ->setStoreId($helper->getStore()->getId())
                            ->load($helper->getRootId());
            }
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__($vendor->getPublicName()." ".('Shop')));
            return $resultPage;
        }
        $this->messageManager->addErrorMessage(__('The Vendor\'s Shop you are trying to access is not available at this moment.'));
        return $this->_redirect('*/*');
   }
}
