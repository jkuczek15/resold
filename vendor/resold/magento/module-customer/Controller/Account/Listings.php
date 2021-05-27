<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Controller\Account;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

class Listings extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $registry,
        Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_queryFactory = $queryFactory;
        $this->layerResolver = $layerResolver;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
        $this->layoutFactory = $layoutFactory;
        $this->_coreRegistry = $registry;
        $this->_catalogLayer = $layerResolver->get();
        $this->session = $customerSession;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Listings'));

        // if($this->getRequest()->getParam('ajax') == 1){
        //
        //   $productCollection = $this->_getProductCollection();
        //   $productHtml = '';
        //
        //   foreach($productCollection as $product)
        //   {
        //     $productHtml .= "<a href='".$product->getProductUrl()."' class='product photo product-item-photo' tabindex='-1'>
        //                     ".$product->getName()."
        //                     </a>";
        //   }// end foreach over product collection
        //
        //   return $this->_resultJsonFactory->create()->setData(['success' => true, 'html' => [
        //       'products_list' => $productHtml
        //   ]]);
        // }// end if ajax

        return $resultPage;
    }

    protected function _getProductCollection()
    {
        $cedLayer = $this->_catalogLayer;

        $vendor = $this->_coreRegistry->registry('current_vendor');
        if($vendor != null){
          $vendorId = $vendor->getId();
        }else{
          $vendorId = $this->session->getVendorId();
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vendorId == null ? -1 : $vendorId);
        $products = [];
        foreach ($collection as $productData) {
            array_push($products, $productData->getProductId());
        }

        $cedProductcollection = $objectManager->create('Magento\Catalog\Model\Product')->getCollection()
                ->addAttributeToSelect($objectManager->get('Magento\Catalog\Model\Config')->getProductAttributes())
                ->addAttributeToFilter('entity_id', ['in'=>$products])
                ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                ->addAttributeToFilter('visibility', 4)
                ->addAttributeToSort('date', 'desc');

        $this->_productCollection = $cedProductcollection;
        /* $pageSize=($this->getRequest()->getParam('product_list_limit'))? $this->getRequest()->getParam('product_list_limit') : 9;
        $this->_productCollection->setPageSize($pageSize);;
        $this->_productCollection->getSelect()->group('e.entity_id'); */
        return $this->_productCollection;
    }
}
