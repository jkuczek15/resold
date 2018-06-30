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

namespace Ced\CsMarketplace\Controller\Vpayments;

class Filter extends \Ced\CsMarketplace\Controller\Vendor
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
        if(!$this->_getSession()->getVendorId()) 
        { 
            return; 
        }
        $reset_filter = $this->getRequest()->getParam('reset_filter');
        $params = $this->getRequest()->getParams();
        if($reset_filter == 1)
            $this->_getSession()->uns('payment_filter');
        else if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
            $this->_getSession()->setData('payment_filter',$params);
        }
        $block = $this->_view->getLayout()
                ->createBlock('Ced\CsMarketplace\Block\Vpayments\Stats')
                ->setTemplate('Ced_CsMarketplace::vpayments/stats.phtml')
                ->setTemplateId('fiter-stats')->toHtml().$this->_view->getLayout()
                ->createBlock('Ced\CsMarketplace\Block\Vpayments\ListBlock')
                ->setTemplate('Ced_CsMarketplace::vpayments/list.phtml')
                ->setTemplateId('fiter-list')->toHtml();
 
                

        $this->getResponse()->setBody($block);
    }
}
