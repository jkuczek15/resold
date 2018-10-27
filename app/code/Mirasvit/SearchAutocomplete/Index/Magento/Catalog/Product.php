<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.65
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Index\Magento\Catalog;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Review\Model\ReviewFactory;
use Mirasvit\SearchAutocomplete\Model\Config;
use Magento\Tax\Model\Config as TaxConfig;
use Mirasvit\SearchAutocomplete\Index\AbstractIndex;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends AbstractIndex
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var ReviewRenderer
     */
    protected $reviewRenderer;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var TaxConfig
     */
    private $taxConfig;

    public function __construct(
        TaxConfig $taxConfig,
        Config $config,
        ReviewFactory $reviewFactory,
        ReviewRenderer $reviewRenderer,
        ImageHelper $imageHelper,
        CatalogHelper $catalogHelper,
        PricingHelper $pricingHelper,
        RequestInterface $request
    ) {
        $this->config = $config;
        $this->reviewFactory = $reviewFactory;
        $this->reviewRenderer = $reviewRenderer;
        $this->imageHelper = $imageHelper;
        $this->catalogHelper = $catalogHelper;
        $this->pricingHelper = $pricingHelper;
        $this->request = $request;
        $this->taxConfig = $taxConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];
        $categoryId = intval($this->request->getParam('cat'));

        $collection = $this->getCollection();

        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('short_description')
            ->addAttributeToSelect('description');

        $this->collection->getSelect()->order('score desc');

        if ($categoryId) {
            $om = ObjectManager::getInstance();
            $category = $om->create('Magento\Catalog\Model\Category')->load($categoryId);
            $collection->addCategoryFilter($category);
        }

        if ($this->config->isShowRating()) {
            $this->reviewFactory->create()->appendSummary($collection);
        }
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $items[] = $this->mapProduct($product);
        }

        return $items;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int $storeId
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function mapProduct($product, $storeId = 1)
    {
        $item = [
            'name'        => $product->getName(),
            'url'         => $product->getProductUrl(),
            'sku'         => null,
            'description' => null,
            'image'       => null,
            'price'       => null,
            'rating'      => null,
            'optimize'    => false,
        ];

        if ($this->config->isShowShortDescription()) {
            $item['description'] = html_entity_decode(
                strip_tags($product->getDataUsingMethod('description'))
            );
        }

        if ($this->config->isShowSku()) {
            $item['sku'] = html_entity_decode(
                strip_tags($product->getDataUsingMethod('sku'))
            );
        }

        $image = false;
        if ($product->getImage() && $product->getImage() != 'no_selection') {
            $image = $product->getImage();
        } elseif ($product->getSmallImage() && $product->getSmallImage() != 'no_selection') {
            $image = $product->getSmallImage();
        }

        if ($this->config->isShowImage()) {
            $item['image'] = $this->imageHelper->init($product, 'product_page_image_small')
                ->setImageFile($image)
                ->resize(65 * 2, 80 * 2)
                ->getUrl();
        }

        if ($this->config->isShowPrice()) {
            $includingTax = true;
            if ($this->taxConfig->getPriceDisplayType() === TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX) {
                $includingTax = false;
            }

            if ($product->getFinalPrice() == 0) {
                $product->setData('final_price', null);
            }

            $price = $product->getFinalPrice();

            if (!$price) {
                $price = $product->getMinimalPrice(); # grouped product
            }

            $item['price'] = $this->catalogHelper->getTaxPrice($product, $price, $includingTax);
            $item['price'] = $this->pricingHelper->currency($item['price'], false, false);
        }

        if ($this->config->isShowRating() && $product->getRatingSummary()) {
            try {
                /** @var \Magento\Store\Model\App\Emulation $emulation */
                $emulation = ObjectManager::getInstance()->get('Magento\Store\Model\App\Emulation');
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);

                /** @var \Magento\Framework\App\State $state */
                $state = ObjectManager::getInstance()->get('Magento\Framework\App\State');

                $state->emulateAreaCode('frontend', function (&$item, $product) {
                    $item['rating'] = $this->reviewRenderer
                        ->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
                }, [&$item, $product]);

                $emulation->stopEnvironmentEmulation();
            } catch (\Exception $e) {
            }
        }

        $om = ObjectManager::getInstance();

        /** @var \Magento\Catalog\Block\Product\ListProduct $productBlock */
        $productBlock = $om->create('Magento\Catalog\Block\Product\ListProduct');
        $item['cart'] = [
            'visible' => $this->config->isShowCartButton(),
            'label'   => __('Add to Cart')->render(),
            'params'  => $productBlock->getAddToCartPostParams($product),
        ];

        return $item;
    }

    /**
     * @param array $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @return mixed
     */
    public function map($documents, $dimensions)
    {
        if (!$this->config->isFastMode()) {
            return $documents;
        }

        $dimension = current($dimensions);
        $storeId = $dimension->getValue();

        $om = ObjectManager::getInstance();

        $productIds = array_keys($documents);

        if (count($productIds) === 0) {
            return $documents;
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $om->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection = $collection->addFinalPrice()
            ->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addFieldToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToFilter('entity_id', ['in' => $productIds]);
        $collection->load();

        /** @var \Magento\Review\Model\ReviewFactory $reviewFactory */
        $reviewFactory = $om->create('Magento\Review\Model\ReviewFactory');
        $reviewFactory->create()->appendSummary($collection);

        foreach ($collection as $product) {
            $entityId = $product->getId();
            $map = $this->mapProduct($product, $storeId);
            $documents[$entityId]['autocomplete'] = $map;
        }

        return $documents;
    }
}
