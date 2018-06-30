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

namespace Ced\CsMarketplace\Block\Vshops\ListBlock;

use Magento\Framework\ObjectManagerInterface;

/**
 * Product list
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_cedCatalogLayer;

    protected $_productCollection;

    public $_prodListHelper;

    /**
     * @var $_vshop
     */
    protected $_vshop;

    /**
     * @var \ObjectManagerInterface $_objectManager
     */
    public $_objectManager;

    /**
     * @var $_carriers
     */
    protected $_carriers = null;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var Vendor Collection
     */
    protected $_vendorCollection;

    /**
     * @var \Magento\Framework\Registry $_coreRegistry
     */
    public $_coreRegistry = null;

    /**
     * Estimate Rates
     * @var array
     */
    protected $_rates = [];

    protected $_checkout = null;

    protected $_quote = null;

    /**
     * ListBlock constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Helper\Product\ProductList $prodListHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        ObjectManagerInterface $objectManager,
        array $data = []
    )
    {
        $this->_cedCatalogLayer = $layerResolver->get();
        $this->_prodListHelper = $prodListHelper;
        $this->urlHelper = $urlHelper;
        $this->_coreRegistry = $context->getRegistry();
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getVendorCollection()
    {
        $vendor_name = $this->_coreRegistry->registry('vendor_name');
        $name_filter = $this->_coreRegistry->registry('name_filter');
        $zip_code = $this->_coreRegistry->registry('zip_code');
        $country = $this->_coreRegistry->registry('country');
        
        if (is_null($this->_vendorCollection)) {
            $vendorIds = [0];
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vshop')->getCollection()
                        ->addFieldToFilter('shop_disable', array('eq'=>\Ced\CsMarketplace\Model\Vshop::DISABLED));
            
            if (count($model) > 0) {
                foreach($model as $row){
                    $vendorIds[] = $row->getVendorId();
                }
            }
            
            $this->_vendorCollection = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')
                            ->getCollection()->addAttributeToSelect('*')
                            ->addAttributeToFilter('status',array('eq'=>\Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS));
            if ($name_filter == '') {    
                $this->_vendorCollection = $this->_vendorCollection->addAttributeToSort('public_name', 'asc');
            }
        
            if (count($vendorIds) > 0) {
                if ($vendor_name!='' || $country!='' || $zip_code!='' || $name_filter!='') {
                    if ($vendor_name != '') {
                        $this->_vendorCollection->addAttributeToFilter(
                                    array(
                                           array('attribute'=>'public_name','like'=>'%'.$vendor_name.'%'),
                                    ) ); 
                    }
                    
                    if ($country != '') {
                        $this->_vendorCollection->addAttributeToFilter(
                                    array( array('attribute'=>'country_id','like'=>'%'.$country.'%') ) ); 
                    }
                    
                    if ($zip_code != '') {
                        $this->_vendorCollection->addAttributeToFilter(
                                    array( array('attribute'=>'zip_code','like'=>'%'.$zip_code.'%') ) ); 
                    }

                    if ($name_filter != '') {
                        $this->_vendorCollection->addAttributeToSort('public_name', $name_filter); 
                    }
                    $this->_vendorCollection = $this->_vendorCollection->addAttributeToFilter('entity_id',array('nin'=>$vendorIds));
                } else {
                    $this->_vendorCollection = $this->_vendorCollection->addAttributeToFilter('entity_id',array('nin'=>$vendorIds));
                }
            }

            if ($this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->isSharingEnabled()){
                $this->_vendorCollection->addAttributeToFilter('website_id',array('eq'=>$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId()));
            }
            $this->prepareSortableFields();
        }

        return $this->_vendorCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedVendorCollection()
    {
        return $this->_getVendorCollection();
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }


    /**
     * Prepare Sort By fields from Category Data for Vshops
     * @return $this
     */
    public function prepareSortableFields()
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($this->_getConfig()->getAttributeUsedForSortByArray());
        }
        $cedAvailableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($defaultSortBy = $this->_getConfig()->getDefaultSortBy()) {
                if (isset($cedAvailableOrders[$defaultSortBy])) {
                    $this->setSortBy($defaultSortBy);
                }
            }
        }
        return $this;
    }

    /**
     * Get catalog layer model
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return $this->_cedCatalogLayer;
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        $currentMode = $this->getChildBlock('toolbar')->getCurrentMode();
        return $currentMode;
    }


    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getVendorCollection();

        // use sortable parameters
        $cedorders = $this->getAvailableOrders();
        if ($cedorders) {
            $toolbar->setAvailableOrders($cedorders);
        }
        $cedsort = $this->getSortBy();
        if ($cedsort) {
            $toolbar->setDefaultOrder($cedsort);
        }
        $ceddir = $this->getDefaultDirection();
        if ($ceddir) {
            $toolbar->setDefaultDirection($ceddir);
        }
        $cedmodes = $this->getModes();
        if ($cedmodes) {
            $toolbar->setModes($cedmodes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getVendorCollection()]
        );

        $this->_getVendorCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToolbarBlock()
    {
        $cedBlockName = $this->getToolbarBlockName();
        if ($cedBlockName) {

            $cedBlock = $this->getLayout()->getBlock($cedBlockName);
            if ($cedBlock) {
                return $cedBlockName;
            }
        }

        $cedBlockName = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $cedBlockName;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        $cedAdditionalHtml = $this->getChildHtml('additional');
        return $cedAdditionalHtml;
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        $cedToolbar = $this->getChildHtml('toolbar');
        return $cedToolbar;
    }

    /**
     * @param Set AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_vendorCollection = $collection;
        return $this;
    }

    /**
     * @param array|string|integer|\Magento\Framework\App\Config\Element $code
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->_getVendorCollection()->addAttributeToSelect($code);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceBlockTemplate()
    {
        $cedPriceBlock = $this->_getData('price_block_template');
        return $cedPriceBlock;
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor');
    }


    /**
     * Get post parameters
     * @param Product $product
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $cedUrl = $this->getAddToCartUrl($product);
        return [
            'action' => $cedUrl,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($cedUrl),
            ]
        ];
    }


    /**
     * @param Product $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();
        $productPrice = '';
        if ($priceRender) {
            $productPrice = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $productPrice;
    }

    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default');
    }

}
