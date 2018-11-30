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

class Index extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->formKeyValidator = $formKeyValidator;
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
      ####################################
      // REQUEST AND USER VALIDATON
      ###################################
      // Ensure user is logged in
      if (!$this->session->isLoggedIn()) {
        return $this->resultJsonFactory->create()->setData(['error' => 'You must be logged in to sell items.']);
      }// end if user not logged in

      // Ensure user is a seller
      if($this->session->getVendorId() == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'Your account must be connected to Stripe to sell items.']);
      }// end if vendor id not set

      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      // when editing, ensure this is the user's post
      $product_id = null;
      if(isset($post['product_id']) && $post['product_id'] != null){
        // get the product id for the listing being updated
        $product_id = $post['product_id'];

        // retreive the seller's product data
        $_product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
        $vendorProduct = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('sku', $_product->getSku())->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();

        if($vendorProduct->getVendorId() !== $this->session->getVendorId()){
          return $this->resultJsonFactory->create()->setData(['error' => "You cannot edit another seller's item."]);
        }// end if this is not the seller's product

      }// end if editing a post

      ####################################
      // FORM VALIDATION
      ###################################
      $local_id = '231';
      $required =  ['local_global',
                    'name',
                    'description',
                    'title_description',
                    'price',
                    'lowestcategory',
                    'condition'];

      if((!isset($post['lowestcategory']) || $post['lowestcategory'] == null) && isset($post['subcategory'])){
        $subcategory = $this->_categoryFactory->create()->getCollection()->addAttributeToFilter('name', $post['subcategory'])->setPageSize(1)->getFirstItem();
        $post['lowestcategory'] = $subcategory->getId();
      }// end if lowest category not set

      // check required fields
      foreach($required as $require){
        if(!isset($post[$require]) || $post[$require] == null || (!is_array($post[$require]) && trim($post[$require]) == null) || (is_array($post[$require]) && count($post[$require]) == 0)){
          return $this->resultJsonFactory->create()->setData(['error' => 'Missing one or more required fields.']);
        }// end if field is not set
      }// end foreach over required fields

      // price validation
      $price = $post['price'];
      if(!is_numeric($price) || $price < 20){
        return $this->resultJsonFactory->create()->setData(['error' => 'Price must be an integer greater than 20.']);
      }// end if invalid price

      // location validation
      $local_global = implode(',', $post['local_global']);
      if($product_id == null && strpos($local_global, $local_id) !== FALSE){
        if(!isset($post['latitude']) || !isset($post['longitude']) || !is_numeric($post['latitude']) || !is_numeric($post['longitude'])){
          return $this->resultJsonFactory->create()->setData(['error' => 'Invalid location.']);
        }// end if latitude longitude not set
      }// end if local global

      // image validation
      $image_paths = $post['image_paths'];
      if($image_paths == ''){
        return $this->resultJsonFactory->create()->setData(['error' => 'At least one image is required.']);
      }else{
        $image_paths = explode(',', $image_paths);
      }// end if no images uploaded

      ####################################
      // SAVE PRODUCT TO DATABASE
      ###################################
      // POST request
      $all_category_id = 105;

      // Clean up the title and title description
      $post['name'] = ucwords(strtolower($post['name']));
      $post['title_description'] = ucfirst($post['title_description']);

      // Set our time zone to Chicago
      date_default_timezone_set('America/Chicago');

      if(!isset($_product))
      {
        // creating a new product
        // Generate a unique product sku, uniqid generates a unique identifier using the current time in microseconds
        // set all of our product attributes and save it to the database
        $sku = uniqid("product-", true);
        $_product = $objectManager->create('Magento\Catalog\Model\Product');
        $_product->setSku($sku);
        $_product->setCreatedAt(strtotime('now'));
        $_product->setCustomAttribute('date', date('m/d/Y h:i:s a', time()));
      }// end if creating a product

      $_product->setName($post['name']);
      $_product->setTypeId('simple');
      $_product->setStoreId(1);
      $_product->setAttributeSetId(4);
      $_product->setVisibility(4);
      $_product->setPrice($post['price']);
      $_product->setDescription(nl2br($post['description']));
      $_product->setCategoryIds([$post['lowestcategory'], $all_category_id]);
      $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
      $_product->setWebsiteIds(array(1));
      $_product->setStockData(['qty' => 1, 'is_in_stock' => true]);
      $_product->setCustomAttribute('title_description', $post['title_description']);
      $_product->setCustomAttribute('condition', $post['condition']);
      $_product->setCustomAttribute('local_global', $local_global);

      // set the location attribute
      if(strpos($local_global, $local_id) !== FALSE && isset($post['latitude']) && $post['latitude'] != null && isset($post['longitude']) && $post['longitude'] != null){
        // local product
        $_product->setCustomAttribute('latitude', $post['latitude']);
        $_product->setCustomAttribute('longitude', $post['longitude']);
      }else{
        $_product->setCustomAttribute('latitude', '');
        $_product->setCustomAttribute('longitude', '');
      }// end if setting local attribute

      // tempory location for product images
      $mediaDir = '/var/www/html/pub/media';

      // get the current product images
      $current_images = $_product->getMediaGallery('images');

      if(($current_images == null || count($current_images) == 0) && count($image_paths) > 0)
      {
        // create the base/primary image
        $primary_path = $mediaDir.$image_paths[0];
        if(strpos($primary_path, "/tmp") !== FALSE)
        {
          if(file_exists($primary_path))
          {
            // uploading a new file
            $_product->addImageToMediaGallery($primary_path, array('image', 'small_image', 'thumbnail'), false, false);
            unset($image_paths[0]);
            unlink($primary_path);
          }// end if file exists
        }// end if uploading a new file
      }// end if number of images == 0

      // loop over all temporary images uploaded for this product
      foreach($image_paths as $image_path)
      {
        // image will be given a new path once linked to the product
        $path = $mediaDir.$image_path;
        if(strpos($path, "/tmp") !== FALSE)
        {
          if(file_exists($path))
          {
            $_product->addImageToMediaGallery($path, null, false, false);
            unlink($path);
          }// end if file exists
        }// end if uploading a new file
      }// end foreach loop over image paths

      // save the product to the database
      $_product->save();

      // link the product to the seller
      if($product_id === null){
        // creating a new product and linking it to the seller
        // save a vendor product with the seller
        $objectManager->get('\Magento\Framework\Registry')->register('saved_product', $_product);
        $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->saveProduct(\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE);
        $this->_eventManager->dispatch('csmarketplace_vendor_new_product_creation', [
          'product' => $_product,
          'vendor_id' => $this->session->getVendorId()
        ]);
      }// end if creating a new product

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute
}
