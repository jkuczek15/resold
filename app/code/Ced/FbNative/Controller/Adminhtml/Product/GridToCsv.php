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

namespace Ced\FbNative\Controller\Adminhtml\Product;


use Ced\FbNative\Helper\Data;
use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManager;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class Render
 */
class GridToCsv extends \Magento\Catalog\Controller\Adminhtml\Product
{
    public $_productPriceIndexerProcessor;

    /**
     * MassActions filter
     *
     * @var Filter
     */
    public $filter;

    /**
     * @var \Magento\Framework\Filesystem
     */
    /**
     * @var CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList $directoryList
     */

    public $directoryList;

    /** @var \Magento\Framework\Filesystem\Io\File $fileIo */

    public $fileIo;
    /** @var Data  */
    public $dataHelper;

    public $storeManager;


    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $fileIo,
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        Filter $filter,
        StoreManager $storeManager,
        Data $dataHelper,
        CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context, $productBuilder);
        $this->fileIo = $fileIo;
        $this->directoryList = $directoryList;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
    }

    public function execute()
    {
        /**
         *  $path is used for to get image path
         */
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
        $mappedAttr = $this->dataHelper->readCsv();
        $resultRedirect = $this->resultRedirectFactory->create();
        if($mappedAttr) {
            foreach ($mappedAttr as $key => $value) {
                if(strpos('facebook_condition', $key) !== FALSE){
                  $key = 'condition';
                }
                $data[] = $key;
            }
        } else {
            $this->messageManager->addErrorMessage(__('First map Attributes in Configuration'));
            $resultRedirect->setPath('fbnative/product/index');
            return $resultRedirect;
        }
        array_push($data, 'id');
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
            $mappedAttr = $this->dataHelper->readCsv();
            if ($attr == 1) {
                $mappedData = array();
                foreach ($mappedAttr as $fbAttr=>$magentoAttr) {
                    $attrValue = $this->dataHelper->getMappedAttributeValue($magentoAttr, $product);
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
                        foreach ($attrValue as $key => $value) {
                            $mappedData[$fbAttr] = $attrValue[$key];
                        }
                    } else {
                        $mappedData[$fbAttr] = $attrValue;
                    }
                    if($fbAttr == 'title'){
                        $mappedData[$fbAttr] = strtolower($attrValue);
                    }
                }

                $default = $this->dataHelper->defaultMappingAttribute($product);
                $mappedData = $default;
                // echo "<pre>";
                // var_dump($mappedData);
                // exit;
                fputcsv($fp, $mappedData);
            }
        }
        //die;
        fclose($fp);
        $this->messageManager->addSuccessMessage(__('Csv Exported successfully'));
        $resultRedirect->setPath('fbnative/product/index');
        return $resultRedirect;
    }
}
