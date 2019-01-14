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
 * @package     Ced_Fyndiq
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\FbNative\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Framework\Message\Manager;
use Magento\Store\Model\StoreManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /** @var \Magento\Catalog\Model\Indexer\Product\Price\Processor $_productPriceIndexerProcessor */
    public $_productPriceIndexerProcessor;

    /** @var Filter $filter */
    public $filter;

    /** @var CollectionFactory  */
    public $collectionFactory;

    /** @var \Magento\Framework\Filesystem\DirectoryList  $directoryList*/
    public $directoryList;

    /** @var \Magento\Framework\Filesystem\Io\File $fileIo */
    public $fileIo;
 /** @var Config */
    public $configHelper;

    public $storeManager;

    public $resultRedirectFactory;

    public $messageManager;

    public $objectManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $fileIo
     * @param Context $context
     * @param Product\Builder $productBuilder
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param Filter $filter
     * @param Config $configHelper
     * @param StoreManager $storeManager
     * @param CollectionFactory $collectionFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param ObjectManager $objectManager
     * @param Manager $manager
     */
    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        Context $context,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        Filter $filter,
        Config $configHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        StoreManager $storeManager,
        CollectionFactory $collectionFactory,
        RedirectFactory $resultRedirectFactory,
        Manager $manager,
        \Magento\Catalog\Model\ProductRepository $productRepository
    )
    {
        parent::__construct($context);
        $this->fileIo = $fileIo;
        $this->directoryList = $directoryList;
        $this->filter = $filter;
        $this->configHelper = $configHelper;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->objectManager = $objectManager;
        $this->messageManager = $manager;
        $this->_productRepository = $productRepository;
    }

    /**
     * @return mixed
     */
    public function productCron() {
        $mediaUrl = BP . "/pub/media/ced_fbnative";
        $url = $this->storeManager->getStore()->getBaseUrl();
        $url = $url.'pub/media/catalog/product';
        $filePath = $mediaUrl . '/export.csv';
        if(!file_exists($mediaUrl)) {
            mkdir($mediaUrl,0777,true);
        }

        $fp = fopen($filePath, "w+");
        $productCollection = $this->collectionFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * To set header title
         */
        $data = [];
        $mappedAttr = $this->readCsv();
        foreach ($mappedAttr as $key => $value) {
            $data[] = $key;
        }
        array_push($data, 'id');
        array_push($data, 'offer_id');
        array_push($data, 'channel');
        array_push($data, 'image_link');
        array_push($data, 'availability');
        // array_push($data, 'sale_price');
        array_push($data, 'product_type');
        array_push($data, 'price');
        array_push($data, 'brand');
        array_push($data, 'description');
        array_push($data, 'link');
        array_push($data, 'item_group_id');
        fputcsv($fp, $data);
        //$default = [];
        foreach ($productCollection as $product) {
            $product = $objectManager->create('Magento\Catalog\Model\Product')->setStoreId(0)->load($product->getId());
            $attr = $product->getData('is_facebook');
            $mappedAttr = $this->readCsv();
            if ($attr == 1) {
                $mappedData = array();
                foreach ($mappedAttr as $fbAttr=>$magentoAttr) {
                    $attrValue = $this->getMappedAttributeValue($magentoAttr, $product);
                    if($magentoAttr == 'image' && $attrValue != '') {
                        $attrValue = $url.$attrValue;
                    }
                    if($magentoAttr == 'price' && $attrValue != '') {
                        $attrValue = 'USD '.$attrValue;
                    }

                    if($magentoAttr == 'google_product_category' && $attrValue == '') {
                        $attrValue = '632';
                    }

                    if(is_array($attrValue)) {
                        $mappedData[$fbAttr] = $attrValue[0];
                    } else {
                        $mappedData[$fbAttr] = $attrValue;
                    }
                }
                $default = $this->defaultMappingAttribute($product);
                $mappedData = array_merge($mappedData, $default);
                fputcsv($fp, $mappedData);
            }
        }
        fclose($fp);
    }

    public function readCsv() {
        $mapped = $this->configHelper->getAttributeMapping();
        $mapped = json_decode($mapped,true);
        $mappedAttr = [];
        if($mapped) {
            foreach ($mapped as $key => $value) {
                if(!$mapped[$key] == null) {
                    foreach ($value as $attr => $item) {
                        //print_r($item);
                        if ($attr == 'facebook_attribute_code') {
                            $fbAttr = $item;
                        } else if ($attr == 'magento_attribute_code') {
                            $magentoAttr = $item;
                        }
                    }
                    $mappedAttr[$fbAttr] = $magentoAttr;
                }
            }
            return $mappedAttr;
        }
        return false;

    }

    public function getMappedAttributeValue ( $magentoAttribute , $product ) {
        $attribute = isset($magentoAttribute) ? $magentoAttribute: '';
        $value = $product->getData($attribute);
        return $value;
    }

    public function defaultMappingAttribute($product) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $url = $baseUrl.'pub/media/catalog/product';
        $productdata = $product->getData();
        $s3base = 'https://s3-us-west-2.amazonaws.com/resold-photos/catalog/product';

        $_product = $this->_productRepository->getById($product->getId());
        $product_url = $_product->getUrlModel()->getUrl($_product);

        // get the products image URL
        $images = $product->getMediaGalleryImages();
        $image = $images->getFirstItem();
        $firstImageUrl = $image->getFile();

        $additionalImages = '';
        foreach($images as $image)
        {
          $additionalImageUrl = $image->getFile();
          if($additionalImageUrl != $firstImageUrl)
          {
            $additionalImages .= $s3base . $additionalImageUrl . ',';
          }// end if not the first image
        }// end foreach loop over images

        // remove the last comma
        $additionalImages = rtrim($additionalImages, ',');

        // get the product condition
        $new_attr_id = 235;
        $condition_id = $product->getCustomAttribute('condition')->getValue();

        if($condition_id == $new_attr_id){
          $condition = 'New';
        }else if($condition_id == $new_attr_id+1){
          $condition = 'Refurbished';
        }else if($condition_id == $new_attr_id+2){
          $condition = 'Refurbished';
        }else if($condition_id == $new_attr_id+3){
          $condition = 'Used';
        }

        $default = [];
        $default[0] = $product->getName();
        $default[1] = $condition;
        $default[2] = $product->getSku();
        $default[3] = $product->getSku();
        $default[4] = $product->getSku();
        $default['image_link'] = $s3base . $firstImageUrl;
        $default['additional_image_link'] = $additionalImages;
        $default['inventory'] = '1';
        $default['availability'] =  $product->isInStock() ? 'In Stock' : 'Out of Stock';
        $default['productType'] = $product->getTypeId();

        if($product->getTypeId()=='configurable') {
            $child = $product->getTypeInstance()->getUsedProducts($product);
            $default['price'] = 'USD '.$child[0]->getPrice();
        } else {
            $default['price'] = isset($productdata['price']) ? 'USD '.$product->getPrice() : 'USD 49';
        }
        $default['brand'] = 'Resold';
        $default['description'] = isset($productdata['description']) ? $productdata['description'] : '';

        $default['link'] = $product_url;
        $confProduct = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($product->getId());
        if($confProduct) {
            $default['item_group_id'] = $confProduct[0];
        } else {
            $default['item_group_id'] = $product->getId();
        }
        return $default;
    }

}
