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
 
namespace Ced\CsMarketplace\Block\Vproducts\Edit;
class Websites extends \Ced\CsMarketplace\Block\Vproducts\Store\Switcher
{
    	protected $_storeFromHtml;

	protected $_objectManager;
   
	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		parent::__construct($context, $objectManager);
		$this->setTemplate('Ced_CsMarketplace::vproducts/edit/websites.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
		$this->_objectManager = $objectManager;
		
	}

    /**
     * Retrieve edited product model instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');
    }

    public function getStoreId()
    {
        return $this->getProduct()->getStoreId();
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getWebsites()
    {
        return $this->getProduct()->getWebsiteIds();
    }

    public function hasWebsite($websiteId)
    {
    	$websiteIds = $this->getProduct()->getWebsiteIds();
        $customerSession = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getCustomerSession();
    	if(!$this->getProduct()->getId() && $customerSession->getFormError() == true){
    		$productformdata = $customerSession->getProductFormData();
    		$websiteIds = isset($productformdata['product']['website_ids']) ? $productformdata['product']['website_ids'] : [];
    	}
        return in_array($websiteId, $websiteIds);
    }

    /**
     * Check websites block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getWebsitesReadonly();
    }

    public function getStoreName($storeId)
    {
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($storeId)->getName();
    }
}

