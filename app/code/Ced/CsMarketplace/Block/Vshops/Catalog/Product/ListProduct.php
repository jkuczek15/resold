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
 * @package   Ced_CsMarketplace
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vshops\Catalog\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{

    /**
     * Retrieve loaded category collection
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    protected function _getProductCollection()
    {   
        $name_filter = $this->_coreRegistry->registry('name_filter');
        if ($this->_productCollection === null) {
           $cedLayer = $this->getLayer();
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            if ($this->_coreRegistry->registry('product')) {
                $cedCategories = $this->_coreRegistry->registry('product')
                                ->getCategoryCollection()->setPage(1, 1)
                                ->load();
                if ($cedCategories->count()) {
                    $this->setCategoryId(current($cedCategories->getIterator()));
                }
            }
            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $cedCategory = $this->categoryRepository->get($this->getCategoryId());
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $cedCategory = null;
                }
                if ($cedCategory) {
                    $origCategory = $cedLayer->getCurrentCategory();
                    $cedLayer->setCurrentCategory($cedCategory);
                }
            }
            $vendorId = $this->_coreRegistry->registry('current_vendor')->getId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $collection = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                        ->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vendorId);
            $products = [];
            foreach ($collection as $productData) {
                array_push($products, $productData->getProductId());
            }
            $cedProductcollection = $objectManager->create('Magento\Catalog\Model\Product')->getCollection()
                    ->addAttributeToSelect($objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes())
                    ->addAttributeToFilter('entity_id', ['in'=>$products])
                    ->addStoreFilter($this->getCurrentStoreId())
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->addAttributeToFilter('visibility', 4);
            
            if (isset($name_filter)) {
                $cedProductcollection->addAttributeToSelect('*')->setOrder('entity_id', $name_filter);
            }
            
            $cat_id = $this->getRequest()->getParam('cat-fil');
            if(isset($cat_id)) {
              $cedProductcollection->joinField(
                  'category_id', 'catalog_category_product', 'category_id',
                  'product_id = entity_id', null, 'left'
              )
              ->addAttributeToSelect('*')
              ->addAttributeToFilter('category_id', array(
                  array('finset', array('in'=>explode(',', $cat_id)))
              ));
            }
              
            $this->_productCollection = $cedProductcollection;
            $this->prepareSortableFieldsByCategory($cedLayer->getCurrentCategory());

            if ($origCategory) {
              $cedLayer->setCurrentCategory($origCategory);
            }
        } 
        /* $pageSize=($this->getRequest()->getParam('product_list_limit'))? $this->getRequest()->getParam('product_list_limit') : 9;
        $this->_productCollection->setPageSize($pageSize);;
        $this->_productCollection->getSelect()->group('e.entity_id'); */
        return $this->_productCollection;
    }


    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    // protected function _prepareLayout()
    // {
    //     parent::_prepareLayout();
    //     if ($this->_getProductCollection()) {
    //         $pager = $this->getLayout()->createBlock(
    //             'Magento\Theme\Block\Html\Pager',
    //             'ced.shopproducts.pager'
    //         )->setAvailableLimit(array(9=>9),array(15=>15),array(30=>30))
    //         ->setShowPerPage(true)
    //         ->setCollection( $this->_getProductCollection());
    //         $this->setChild('products.pager', $pager);
    //         $this->_getProductCollection()->load();
    //     }
    //     return $this;
    // }

    // public function getPagerHtml()
    // {
    //     return $this->getChildHtml('products.pager');
    // }
}
