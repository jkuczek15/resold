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
 * @category  Ced
 * @package   Ced_Facebook
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Facebook\Block\Product;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class ListProduct
 * @package Ced\Facebook\Block\Product
 */
class ListProduct extends /*\Magento\Catalog\Block\Product\ListProduct*/
    \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $objectManager;
    /**
     * @var array
     */
    protected $_priceBlock = [];

    /**
     * @var bool
     */
    protected $_useLinkForAsLowAs = true;

    /**
     * @var int
     */
    protected $_defaultColumnCount = 3;

    /**
     * Product amount per row depending on custom page layout of category
     *
     * @var array
     */
    protected $_columnCountLayoutDepend = [];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $_mathRandom;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $_compareProduct;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var ReviewRendererInterface
     */
    protected $reviewRenderer;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutHelper;

    /**
     * ListProduct constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = [],
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Ui\Component\MassAction\Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->urlHelper = $urlHelper;
        $this->cartHelper = $cartHelper;
        $this->filter = $filter;
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);

    }

    /**
     * @return \Magento\Checkout\Helper\Cart
     */
    public function getCartHelper()
    {
        return $this->cartHelper;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout');
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->_checkoutSession->getQuote()->validateMinimumAmount();
    }

    /**
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        return $this->_checkoutHelper->canOnepageCheckout();
    }

    /**
     * @return reward
     */
    protected function _getProductCollection()
    {
        $collection = $this->objectManager->create('Magento\Catalog\Model\Product')->getCollection();
        $collection->addAttributeToFilter('is_facebook', 1);
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'reward.history.pager')
        ->setAvailableLimit(array(15 => 15))->setShowPerPage(true)->setCollection($collection);
        $this->setChild('pager', $pager);
        return $collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return mixed
     */
    public function getFilteredProducts()
    {
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 10;

        $query = ($this->getRequest()->getParam('q')) ? $this->getRequest()->getParam('q') : false;

        if ($query) {
            $collection = $this->objectManager->create('Magento\Catalog\Model\Product')->getCollection();
            $collection->addAttributeToFilter(
                [
                    ['attribute' => 'name', 'like' => '%' . $query . '%'],
                    ['attribute' => 'description', 'like' => '%' . $query . '%']
                ]);
        } else {
            $collection = $this->objectManager->create('Magento\Catalog\Model\Product')->getCollection();
        }

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        return $collection;
    }

    /**
     * @return reward
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    public function isRedirectToCartEnabled()
    {
        return $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $product
     * @return string
     */
    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }

    /**
     * @param $product
     * @param $imageId
     * @param array $attributes
     * @return mixed
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->objectManager->create('\Magento\Catalog\Block\Product\ImageBuilder')
            ->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * @param $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }
        return $this->cartHelper->getAddUrl($product, $additional);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * @param $product
     * @param array $additional
     * @return mixed
     */
    public function getAddUrl($product, $additional = [])
    {

        $continueUrl = $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl());
        $urlParamName = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;

        $routeParams = [
            $urlParamName => $continueUrl,
            'product' => $product->getEntityId(),
            '_secure' => $this->_getRequest()->isSecure()
        ];

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_scope'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_scope_to_url'] = true;
        }

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart'
        ) {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }

    /**
     * @return mixed
     */
    public function getCurrentSearchUrl()
    {
        return $this->_storeManager->getStore()->getCurrentUrl();
    }

    /**
     * @return bool|string
     */
    public function getBannerUrl()
    {
        if (trim($this->_scopeConfig->getValue('facebookconfiguration/facebooksetting/upload_image_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)))
            return $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'ced/facebook/banner/' . $this->_scopeConfig->getValue('facebookconfiguration/facebooksetting/upload_image_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return false;

    }
}
