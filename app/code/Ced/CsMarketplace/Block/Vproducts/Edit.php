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

namespace Ced\CsMarketplace\Block\Vproducts;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	protected $urlModel;

	protected $session;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,		
		UrlFactory $urlFactory
    ){
		
		$this->session = $customerSession;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
        parent::__construct($context,$customerSession,$objectManager,$urlFactory);
		$vendorId=$this->getVendorId();
		$id=$this->getRequest()->getParam('id');
		$status=0;
		if($id){
			$vproductsCollection =$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('',$vendorId,$id);
			$status=$vproductsCollection->getFirstItem()->getCheckStatus();
		}
		$storeId=0;
		if($this->getRequest()->getParam('store')){
			$websiteId=$this->_objectManager->get('Magento\Store\Model\Store')->load($this->getRequest()->getParam('store'))->getWebsiteId();
			if($websiteId){
				if(in_array($websiteId,$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getAllowedWebsiteIds())){
					$storeId=$this->getRequest()->getParam('store');
				}
			}
		}
		$product = $this->_objectManager->get('Magento\Catalog\Model\Product')->setStoreId($storeId);
		if($id){
			$product = $product->load($id);
		}
		$this->setVproduct($product);
		$registry=$this->_objectManager->get('Magento\Framework\Registry');
		$registry->register('current_product',$product);		
		$this->setCheckStatus($status);
    }
	
	
	public function getDeleteUrl($product)
	{
		return $this->getUrl('*/*/delete', array('id' => $product->getId(),'_secure'=>true,'_nosid'=>true));
	}
	
	public function getBackUrl()
	{
		return $this->getUrl('*/*/index',array('_secure'=>true,'_nosid'=>true));
	}
	
	public function getDownloadableProductLinks($_product){
		return $this->_objectManager->get('Magento\Downloadable\Model\Product\Type')->getLinks($_product);
	}
	
	public function getDownloadableHasLinks($_product){
		return $this->_objectManager->get('Magento\Downloadable\Model\Product\Type')->hasLinks($_product);
	}
	
    public function getDownloadableProductSamples($_product){
		return $this->_objectManager->get('Magento\Downloadable\Model\Product\Type')->getSamples($_product);
	}
	
	public function getDownloadableHasSamples($_product){
		return $this->_objectManager->get('Magento\Downloadable\Model\Product\Type')->hasSamples($_product);
	}
	public function createBlock($class)
	{
		return $this->_objectManager->get($class);
	}
}
