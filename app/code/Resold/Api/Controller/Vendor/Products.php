<?php
/**
 * Resold
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category    Resold
 * @package     Resold
 * @author      Resold Core Team <dev@resold.us>
 * @copyright   Copyright Resold (https://resold.us/)
 * @license     https://resold.us/license-agreement
 */
namespace Resold\Api\Controller\Vendor;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use Ced\CsMarketplace\Model\VendorFactory;

class Products extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        VendorFactory $Vendor
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->vendor = $Vendor;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $resultRedirect = $this->resultRedirectFactory->create();
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      if(!isset($post['vendorId']) || $post['vendorId'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if vendor ID is set

      if(!isset($post['type']) || $post['type'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if vendor ID is set

      if($post['vendorId'] == '-1') {
        return $this->resultJsonFactory->create()->setData(['error' => 'You have not listed any items for sale.']);
      }// end if vendor Id == -1

      // get vendor products
      $vendorProductIds = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()
        ->addFieldToFilter('vendor_id', $post['vendorId'])
        ->addFieldToFilter('check_status', 1)
        ->addFieldToSelect('product_id');

      $productIds = [];
      foreach ($vendorProductIds as $productRs) {
        $productIds[] = $productRs['product_id'];
      }// end foreach loop over vendor product ids

      $vendorProducts = $objectManager->create('Magento\Catalog\Model\Product')->getCollection()
        ->addAttributeToSelect($objectManager->create('Magento\Catalog\Model\Config')->getProductAttributes())
        ->addAttributeToSelect('sku')
        ->addAttributeToSelect('latitude')
        ->addAttributeToSelect('longitude')
        ->addAttributeToSelect('description')
        ->addAttributeToSelect('title_description')
        ->addAttributeToSelect('local_global')
        ->addAttributeToSelect('condition')
        ->addAttributeToSelect('charge_id')
        ->addAttributeToSelect('delivery_id')
        ->addAttributeToFilter('entity_id',array('in' => $productIds))
        ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

      $result = [];
      foreach($vendorProducts as $product) {

        $titleDescription = $product->getCustomAttribute('title_description');
        $localGlobal = $product->getCustomAttribute('local_global');
        $condition = $product->getCustomAttribute('condition');
        $latitude = $product->getCustomAttribute('latitude');
        $longitude = $product->getCustomAttribute('longitude');
        $chargeId = $product->getCustomAttribute('charge_id');
        $deliveryId = $product->getCustomAttribute('delivery_id');

        $categoryIds = $product->getCategoryIds();
        $productResult = [
          'id' => $product->getId(),
          'sku' => $product->getSku(),
          'name' => $product->getName(),
          'price' => $product->getPrice(),
          'description' => $product->getDescription(),
          'image' => $product->getImage(),
          'small_image' => $product->getSmallImage(),
          'thumbnail' => $product->getThumbnail(),
          'category_ids' => $product->getCategoryIds(),
          'title_description' => $titleDescription ? $titleDescription->getValue() : null,
          'local_global' => $localGlobal ? $localGlobal->getValue() : null,
          'condition' => $condition ? $condition->getValue() : null,
          'latitude' => $latitude ? $latitude->getValue() : null,
          'longitude' => $longitude ? $longitude->getValue() : null,
          'charge_id' => $chargeId ? $chargeId->getValue() : null,
          'delivery_id' => $deliveryId ? $deliveryId->getValue() : null
        ];

        if($post['type'] == 'for-sale') {
          if(!empty($categoryIds)) {
            $result[] = $productResult;
          }
        } else {
          if(empty($categoryIds)) {
            $result[] = $productResult;
          }
        }// end if type == 'for-sale'
      }// end foreach loop over vendor products

      return $this->resultJsonFactory->create()->setData($result);
    }// end function execute
}
