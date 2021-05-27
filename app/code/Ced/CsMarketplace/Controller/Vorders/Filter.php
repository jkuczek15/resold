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

namespace Ced\CsMarketplace\Controller\Vorders;

class Filter extends \Ced\CsMarketplace\Controller\Vorders
{
    /**
* 
     *
 * @var \Magento\Framework\View\Result\Page 
*/
    protected $resultPageFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    	\Magento\Customer\Model\Session $customerSession,
    	\Magento\Framework\UrlFactory $urlFactory,
    	\Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        \Ced\CsMarketplace\Model\Session $mktSession,
        \Ced\CsMarketplace\Model\Vorders $vorders
    ) {
    
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager,$registry,$mktSession,$vorders);
    }
    public function execute()
    {   
        if(!$this->_getSession()->getVendorId()){ 
            return; 
        }
        $reset_filter = $this->getRequest()->getParam('reset_order_filter');
        $params = $this->getRequest()->getParams();
        if($reset_filter == 1)
            $this->_getSession()->uns('order_filter');
        else if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ){
            $this->_getSession()->setData('order_filter',$params);
        }
        $block = $this->_view->getLayout()
                ->createBlock('Ced\CsMarketplace\Block\Vorders\ListOrders')
                ->setTemplate('Ced_CsMarketplace::vorders/list.phtml')
                ->toHtml();

        $this->getResponse()->setBody($block);
    }
}
