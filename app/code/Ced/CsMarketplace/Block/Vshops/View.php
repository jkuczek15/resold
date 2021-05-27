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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsMarketplace\Block\Vshops;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_vendorCollection;

    protected $_vshop;

    public $_objectManager;

    protected $_vendor;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,

        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        if ($this->getVendor()) {
            $vendor = $this->getVendor();
            if ($vendor->getMetaDescription())
                $this->pageConfig->setDescription($vendor->getMetaDescription());
            if ($vendor->getMetaKeywords())
                $this->pageConfig->setKeywords($vendor->getMetaKeywords());
        }

    }

    public function getVendor()
    {
        if (!$this->_vendor)
            $this->_vendor = $this->_coreRegistry->registry('current_vendor');
        return $this->_vendor;
    }

    public function camelize($key)
    {
        return $this->_camelize($key);
    }

    protected function _camelize($name)
    {
        $this->uc_words($name, '');
    }

    function uc_words($str, $destSep = '_', $srcSep = '_')
    {
        return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
    }

    public function getLeftProfileAttributes($storeId = null)
    {
        if ($storeId == null) $storeId = $this->_storeManager->getStore()->getId();
        $attributes = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Attribute')
            ->setStoreId($storeId)
            ->getCollection()
            ->addFieldToFilter('use_in_left_profile', ['gt' => 0])
            ->setOrder('position_in_left_profile', 'ASC');
        $this->_eventManager->dispatch('ced_csmarketplace_left_profile_attributes_load_after', array('attributes' => $attributes));
        return $attributes;
    }


    /**
     * @return mixed
     */
    public function getVendorLogo()
    {
        return $this->getVendor()->getData('profile_picture');
    }

    /**
     * @return mixed
     */
    public function getVendorBanner()
    {
        return $this->getVendor()->getData('company_banner');
    }


    /**
     * @param $code
     * @return bool
     */

    public function Method($code)
    {

        if ($this->getVendor()->getData($code) != "")
            return $this->getVendor()->getData($code);
        else
            return false;
    }

}

