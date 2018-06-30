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

namespace Ced\CsMarketplace\Controller\Vproducts;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class DeleteImage extends \Ced\CsMarketplace\Controller\Vproducts
{
    public $resultJsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if (!$this->_getSession()->getVendorId()) {
            return false;
        }
        $data = $this->getRequest()->getParams();

        $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')
            ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        try {

            $entryResolver = $this->_objectManager
                ->create('Magento\Catalog\Model\Product\Gallery\EntryResolver');

            $product = $this->_objectManager
                ->create('Magento\Catalog\Model\Product')->setStoreId($data['storeid'])->load($data['productid']);
            $entryId = $entryResolver->getEntryIdByFilePath($product, $data['imagename']);

            $this->_objectManager
                ->create('Magento\Catalog\Model\Product\Gallery\GalleryManagement')
                ->remove($product->getSku(), $entryId);
            $result = 1;

            /* $items = $mediaApi->items($data['productid']);
            if(is_array($items) && count($items)>0 && isset($data['imagename'])){
             foreach($items as $item){
              if($item['file']==$data['imagename']){
	        			$mediaApi->remove($data['productid'], $item['file']);
	        			$result=1;
              }
            } 
			
            if($product && $product->getId() && isset($data['imagename'])){
            if($data['imagename']==$product->getImage()){
            $product->setStoreId($data['storeid'])->setImage(null)->setThumnail(null)->setSmallImage(null)->save();
            $result=1;
            }
            }}
            */
        } catch (\Exception $e) {
            $result = 0;
        }
        $resultJson->setData($result);
        return $resultJson;
    }
}
