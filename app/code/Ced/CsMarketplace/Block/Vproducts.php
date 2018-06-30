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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block;

use Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;

class Vproducts extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    protected $_filtercollection;
    public $_objectManager;
    protected $_type;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Vproducts constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Type $type
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type $type,
        \Magento\Framework\Module\Manager $moduleManager,
        Session $customerSession,
        UrlFactory $urlFactory
    )
    {
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
        $this->_objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->_type = $type;
        $vendorId = $this->getVendorId();
        $appState = $context->getAppState();
        $this->appstate = $appState;

        $currentStore = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId();
        $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $productcollection = $this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection();

        $storeId = 0;
        if ($this->getRequest()->getParam('store')) {
            $websiteId = $this->_objectManager->get('Magento\Store\Model\Store')
                        ->load($this->getRequest()->getParam('store'))->getWebsiteId();
            if ($websiteId) {
                if (in_array($websiteId, $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getAllowedWebsiteIds())) {
                    $storeId = $this->getRequest()->getParam('store');
                }
            }
        }
        $productcollection->addAttributeToSelect('sku')
                        ->addAttributeToSelect('name')
                        ->addAttributeToSelect('price')
                        ->addAttributeToSelect('type_id')
                        ->addAttributeToSelect('small_image')
                        ->addAttributeToSort('entity_id', 'DESC');
        
        $productcollection->addStoreFilter($storeId);
        $productcollection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId);
        $productcollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId);
        $productcollection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId);
        $productcollection->joinAttribute('thumbnail', 'catalog_product/thumbnail', 'entity_id', null, 'left', $storeId);

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $productcollection->joinField('qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        $productcollection->joinField('check_status', 'ced_csmarketplace_vendor_products', 'check_status', 'product_id=entity_id','{{table}}.vendor_id='.$vendorId, 'right');
        $params = $this->session->getData('product_filter');

        if (isset($params) && is_array($params) && count($params) > 0) {
            foreach ($params as $field => $value) {
                if ($field == 'store' || $field == 'store_switcher' || $field == "__SID" || $field == 'reset_product_filter')
                    continue;
                if (is_array($value)) {
                    if (isset($value['from']) && urldecode($value['from']) != "") {
                        $from = urldecode($value['from']);
                        $productcollection->addAttributeToFilter($field, array('gteq' => $from));
                    }
                    if (isset($value['to']) && urldecode($value['to']) != "") {
                        $to = urldecode($value['to']);
                        $productcollection->addAttributeToFilter($field, array('lteq' => $to));
                    }
                } else if (urldecode($value) != "") {
                    $productcollection->addAttributeToFilter($field, array("like" => '%' . urldecode($value) . '%'));
                }
            }
        }

        $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore($currentStore);
        $productcollection->setStoreId($storeId);
    
        if ($productcollection->getSize() > 0) {
            $this->_filtercollection = $productcollection;
            $this->setVproducts($this->_filtercollection);
        }
        
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->_filtercollection) {
            if ($this->_filtercollection->getSize() > 0) {
                if ($this->getRequest()->getActionName() == 'index') {
                    $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'custom.pager');
                    $pager->setAvailableLimit(array(5 => 5, 10 => 10, 20 => 20, 'all' => 'all'));
                    $pager->setCollection($this->_filtercollection);
                    $this->setChild('pager', $pager);
                }
            }
        }
        return $this;
    }

    /**
     * Get pager HTML
     *
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * get Edit product url
     * @param $product
     * @return string
     */
    public function getEditUrl($product)
    {
        return $this->getUrl('*/*/edit', array('_nosid' => true, 'id' => $product->getId(), 'type' => $product->getTypeId(), 'store' => $this->getRequest()->getParam('store', 0)));
    }

    public function getTypes()
    {
        return $this->_type->toOptionArray(false, true);
    }

    /**
     * get Product Type url
     *
     */
    public function getProductTypeUrl()
    {
        return $this->getUrl('*/*/new/', array('_nosid' => true));
    }


    /**
     * get Delete url
     * @param $product
     * @return string
     */
    public function getDeleteUrl($product)
    {
        return $this->getUrl('*/*/delete', array('_nosid' => true, 'id' => $product->getId()));
    }

    /**
     * back Link url
     *
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    /**
     * get Product
     *
     */

    public function getProduct()
    {
        return $this->_objectManager->get('Magento\Framework\Registry')->registry('current_product');
    }

    /**
     * get Category IDs
     *
     */
    public function getCategoryIds()
    {
        $_product = $this->getProduct();
        $category_ids = [];
        if ($_product) {
            $category_ids = $_product->getCategoryIds();
        }
        if (is_array($category_ids) && empty($category_ids)) {
            $category_ids = [];
        }
        return $category_ids;
    }

}
