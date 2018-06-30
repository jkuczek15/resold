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

namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

Class SaveVproductAttributesData implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request
    ) {
        $this->_objectManager = $objectManager;
        $this->request = $request;
    }
    
    /**
     * Adds catalog categories to top menu
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $productIds = $this->_objectManager->get('Magento\Catalog\Helper\Product\Edit\Action\Attribute')->getProductIds();
        if (is_array($productIds)) {
            $inventoryData = $this->request->getParam('inventory', []);
            $attributesData = $this->request->getParam('attributes', []);
            $websiteRemoveData = $this->request->getParam('remove_website_ids', []);
            $websiteAddData = $this->request->getParam('add_website_ids', []);
            if (!empty($attributesData)) {
                $productData['product'] = $attributesData; 
            }
            $vproductsModel = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts');
            $collection = $vproductsModel->getCollection()->addFieldToFilter('product_id', ['in'=>$productIds]);
            if (count($collection) > 0) {
                foreach ($collection as $row) {                    
                    $oldWebsiteIds = explode(',', $row->getWebsiteIds());
                    $websiteIds = implode(',', array_unique(array_filter(array_merge(array_diff($oldWebsiteIds, $websiteRemoveData), $websiteAddData))));
                    $row->addData($productData['product']);
                    if (isset($productData['product']['stock_data'])) {
                        $productData['product']['stock_data'] = $inventoryData;
                        $row->addData($productData['product']['stock_data']);
                    }
                    if (isset($productData['product']['status'])) {
                        $row->setStoreId($this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParam('store', 0));
                        $row->setStatus($productData['product']['status']);
                    }
                    $vproductsModel->extractNonEditableData($row);
                    $row->addData(array('website_ids'=>$websiteIds));
                    $row->save();
                }
            }
        }
    }
}
