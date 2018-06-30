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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProduct\Controller\Vproducts;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

class Index extends \Ced\CsMarketplace\Controller\Vproducts\Index
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
	
	protected $_scopeConfig;
	protected $_storeManager;
	protected $_httpRequest;
	protected $resultPageFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        Session $customerSession,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
         UrlFactory $urlFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $httpRequest
    ) {
        $this->stockFilter = $stockFilter;
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
        $this->productBuilder = $productBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_session = $context->getSession();
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_httpRequest = $httpRequest; 
    }

	public function execute() {
		if(!$this->_getSession()->getVendorId()) {
            return $this->_redirect('csmarketplace/vendor/index');
        }
		$this->_scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		
		$storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
		$this->_httpRequest = $this->_objectManager->get('Magento\Framework\App\Request\Http');
		$module_name = $this->_httpRequest->getModuleName();
		if($this->_scopeConfig->getValue('ced_csmarketplace/general/ced_vproduct_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
				&& $module_name=='csmarketplace'){
			return $this->_redirect('csmarketplace/vendor/index');
		}
		if(!$this->_scopeConfig->getValue('ced_csmarketplace/general/ced_vproduct_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) && $module_name == 'csproduct'){
			return $this->_redirect('csmarketplace/vendor/index');

		}
		if(!$this->_scopeConfig->getValue('ced_csmarketplace/general/ced_vproduct_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId) && $module_name =="csmarketplace")
		{
			return parent::execute();
		}

		$resultPage = $this->resultPageFactory->create();
		$resultRedirect = $this->resultRedirectFactory->create();
		
		$resultPage->getConfig()->getTitle()->prepend(__('Products'));
		$resultPage->getConfig()->getTitle()->prepend(__('Manage Product'));
		return $resultPage;
	}
}
