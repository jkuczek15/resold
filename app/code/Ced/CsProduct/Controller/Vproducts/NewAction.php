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
use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\UrlFactory;

class NewAction extends \Ced\CsMarketplace\Controller\Vproducts\NewAction
{
    /**
     * @var Initialization\StockDataFilter
     */
    protected $stockFilter;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @var \Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;


    /**
     * NewAction constructor.
     * @param Action\Context $context
     * @param Product\Builder $productBuilder
     * @param Session $customerSession
     * @param Product\Initialization\StockDataFilter $stockFilter
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
		\Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
		Session $customerSession,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
		 UrlFactory $urlFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
    	\Magento\Store\Model\StoreManagerInterface $_storeManager,
		\Magento\Framework\Module\Manager $moduleManager,
		\Ced\CsProduct\Helper\Data $helper
    ) {
		$this->stockFilter = $stockFilter;
		$this->helper = $helper;
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager,$_storeManager);
		$this->productBuilder = $productBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
		$this->_session = $context->getSession();	
		$this->clean();
    }

    /**
     * Create new product page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    
	
	public function execute()
	{
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
		
		if (!$this->getRequest()->getParam('set')) {
			 $this->_redirect('csmarketplace/vendor/index');
		}
		
		$this->_scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
	
		$attributesetIds = $this->_scopeConfig->getValue('ced_vproducts/general/set', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if(!$attributesetIds)
		{
			$this->messageManager->addError(__('No Attribute Set Is Allowed For Product Creation.'));
			return $this->_redirect('csmarketplace/vendor/index');

		}
		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$allowedType = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type')->getAllowedType($storeManager->getStore()->getId());
	    if(!in_array($this->getRequest()->getParam('type'),$allowedType))
	    {
	    	$this->messageManager->addError(__('You are not allowed to create '.ucfirst(($this->getRequest()->getParam('type')).' Product')));
	    	return $this->_redirect('csmarketplace/vendor/index');

	    }	
	
		$product = $this->productBuilder->build($this->getRequest());
		$this->_eventManager->dispatch('catalog_product_new_action', ['product' => $product]);
		$this->_eventManager->dispatch('csproduct_vproducts_new_action', ['product' => $product]);
	
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$resultPage = $this->resultPageFactory->create();
		if ($this->getRequest()->getParam('popup')) {
			$resultPage->addHandle(['popup', 'csproduct_vproducts_' . $product->getTypeId()]);
		} else {
			$resultPage->addHandle(['csproduct_vproducts_' .$product->getTypeId()]);
			$resultPage->getConfig()->getTitle()->prepend(__('Products'));
			$resultPage->getConfig()->getTitle()->prepend(__('New Product'));
		}
	
		$block = $resultPage->getLayout()->getBlock('catalog.wysiwyg.js');
		if ($block) {
			$block->setStoreId($product->getStoreId());
		}
	
		return $resultPage;
	}
	public function clean(){
       $this->helper->cleanCache();
       return $this;
        
    }
}


