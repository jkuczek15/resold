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
namespace Custom\Api\Controller\Product;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class AcceptOffer extends \Magento\Framework\App\Action\Action
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
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\Registry $registry
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post) || !isset($post['product_id'])){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      if(!isset($post['offer_price']) || !is_numeric($post['offer_price'])){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if invalid offer price

      if(!isset($post['receiver_id']) || $post['receiver_id'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if invalid offer price

      $receiver_id = $post['receiver_id'];
      $product_id = $post['product_id'];
      $offer_price = $post['offer_price'];

      // retreive the seller's product data
      $_product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
      $vendorProduct = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('sku', $_product->getSku())->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();

      if($vendorProduct->getVendorId() !== $this->session->getVendorId()){
        return $this->resultJsonFactory->create()->setData(['error' => "You cannot edit another seller's item."]);
      }// end if this is not the seller's product

      // update the list price
      $_product->setPrice($offer_price);
      $_product->save();

      $link = " <a href='".$_product->getProductUrl()."'>Click here to view updated listing price.</a>";
      $customer_name = $this->session->getCustomer()->getName();
      $result = [
        'email_subject' => $_product->getName(),
        'text_email' => $customer_name . ' has accepted your offer of <strong>$'.trim(money_format('%(#10n', $offer_price)).'</strong>.'.$link,
        'vendor_id' => $receiver_id,
        'reply' => true,
        'accept_offer' => true,
        'product_id' => $product_id,
        'seller_cust_id' => $this->session->getId()
      ];
      return $this->resultJsonFactory->create()->setData($result);
    }// end function execute
}
