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

namespace Ced\CsMarketplace\Controller\Vproducts;

class Filter extends \Ced\CsMarketplace\Controller\Vproducts
{

    protected $resultPageFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	\Magento\Customer\Model\Session $customerSession,
    	\Magento\Framework\UrlFactory $urlFactory,
    	\Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
    }

    public function execute()
    {   
        $customerSession = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getCustomerSession();
        if (!$customerSession->getVendorId()) { 
            return; 
        }
        $reset_filter = $this->getRequest()->getParam('reset_product_filter');
      
        $params = $this->getRequest()->getParams();
        
        if ($reset_filter == 1) {
            $customerSession->uns('product_filter'); 
        }elseif(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ) {
            $customerSession->setData('product_filter', $params); 
        }
   		 $this->_redirect('*/*/index/store/'.$this->getRequest()->getParam('store_switcher', 0));
         return;
    }
}
