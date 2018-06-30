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
 
namespace Ced\CsMarketplace\Block\Vproducts\Store;

class Switcher extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $_storeIds;
	protected $_objectManager;
    protected $_storeVarName = 'store';

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;
   
	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager,
            array $data = []
	) {
		parent::__construct($context,$data);
		$this->setTemplate('Ced_CsMarketplace::vproducts/store/switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName(__('Default Values'));
		$this->_objectManager = $objectManager;
		
	}
    /**
     * Deprecated
     */
    public function getWebsiteCollection()
    {
        $collection = $this->_objectManager->get('Magento\Store\Model\Website')->getResourceCollection();

        $websiteIds = $this->getWebsiteIds();
        if (!is_null($websiteIds)) {
            $collection->addIdFilter($this->getWebsiteIds());
        }

        return $collection->load();
    }

    /**
     * Get websites
     *
     * @return array
     */
    public function getWebsites()
    {
		$websites = $this->_objectManager->get('Magento\Store\Model\WebsiteFactory')->create()->getCollection()->toOptionHash();
        $websiteIds=$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getAllowedWebsiteIds();
    	if ($this->_objectManager->get('Magento\Framework\Registry')->registry('current_product') != null) {
    		$product = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');
    		$prowebsites = $product->getWebsiteIds();
    		if(is_array($prowebsites) && count($prowebsites)){
        		$websiteIds = array_unique(array_intersect($websiteIds,$prowebsites));
    		} 
    	}
        if ($websiteIds) {
            foreach ($websites as $websiteId => $website) {
                if (!in_array($websiteId, $websiteIds)) {
                    unset($websites[$websiteId]);
                } else {
					$websites[$websiteId] = $this->_objectManager->get('Magento\Store\Model\WebsiteFactory')->create()->load($websiteId);
				}
            }
        }
	
        return $websites;
    }

    /**
     * Deprecated
     */
    public function getGroupCollection($website)
    {
        if (!$website instanceof \Magento\Store\Model\Website) {
			
            $website = $this->_objectManager->get('Magento\Store\Model\Website')->load($website);
        }
		
        return $website->getGroupCollection();
    }


    /**
     * Get store groups for specified website
     * @param $website
     * @return \Magento\Store\Model\Store[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreGroups($website)
    {
        if (!$website instanceof \Magento\Store\Model\Website) {
            $website = $this->_storeManager->getWebsite($website);
        }
        return $website->getGroups();
    }

    /**
     * Deprecated
     */
    public function getStoreCollection($group)
    {
		
        if (!$group instanceof \Magento\Store\Model\Group) {
            $group = $this->_objectManager->get('Magento\Store\Model\Group')->load($group);
        }
        $stores = $group->getStoreCollection();
        $_storeIds = $this->getStoreIds();
        if (!empty($_storeIds)) {
            $stores->addIdFilter($_storeIds);
        }
        return $stores;
    }

    /**
     * Get store views for specified store group
     *
     * @param \Magento\Store\Model\Group $group
     * @return array
     */
    public function getStores($group)
    {
		
        if (!$group instanceof \Magento\Store\Model\Group) {
            $group = $this->_storeManager->getGroup($group);
        }
        $stores = $group->getStores();
        if ($storeIds = $this->getStoreIds()) {
            foreach ($stores as $storeId => $store) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }
        return $stores;
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, $this->_storeVarName => null,'_secure'=>true,'_nosid'=>true));
    }

    public function setStoreVarName($varName)
    {
        $this->_storeVarName = $varName;
        return $this;
    }

    public function getStoreId()
    {
        return $this->getRequest()->getParam($this->_storeVarName);
    }

    public function setStoreIds($storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    public function getStoreIds()
    {
        return $this->_storeIds;
    }

    public function isShow()
    {
        return true;
    }

    protected function _toHtml()
    {
       	return parent::_toHtml();
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if (null !== $hasDefaultOption) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }
}
