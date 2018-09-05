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
        JsonFactory $resultJsonFactory
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      // Redirect users back to the sell form on validation errors
      $resultRedirect = $this->resultRedirectFactory->create();

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
      if(strpos($local_global, $local_id) !== FALSE){
        if(!isset($post['latitude']) || !isset($post['longitude']) || !is_numeric($post['latitude']) || !is_numeric($post['longitude'])){
          return $this->resultJsonFactory->create()->setData(['error' => 'Invalid location.']);
        }// end if latitude longitude not set
      }// end if local global

      // image validation
      $images = $_FILES['images']['name'];
      if(count($images) == 0){
        return $this->resultJsonFactory->create()->setData(['error' => 'At least one image is required.']);
      }// end if no images uploaded

      ####################################
      // SAVE PRODUCT TO DATABASE
      ###################################
      // POST request
      $all_category_id = 105;
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $_product = $objectManager->create('\Magento\Catalog\Model\Product');

      // Clean up the title and title description
      $post['name'] = ucwords(strtolower($post['name']));
      $post['title_description'] = ucfirst($post['title_description']);

      // Set our time zone to Chicago
      date_default_timezone_set('America/Chicago');

      // Generate a unique product sku, uniqid generates a unique identifier using the current time in microseconds
      // set all of our product attributes and save it to the database
      $sku = uniqid("product-", true);
      $_product = $objectManager->create('Magento\Catalog\Model\Product');
      $_product->setName($post['name']);
      $_product->setSku($sku);
      $_product->setTypeId('simple');
      $_product->setAttributeSetId(4);
      $_product->setVisibility(4);
      $_product->setPrice($post['price']);
      $_product->setDescription(nl2br($post['description']));
      $_product->setCategoryIds([$post['lowestcategory'], $all_category_id]);
      $_product->setCreatedAt(strtotime('now'));
      $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
      $_product->setWebsiteIds(array(1));
      $_product->setStockData(['qty' => 1, 'is_in_stock' => true]);
      $_product->setCustomAttribute('title_description', $post['title_description']);
      $_product->setCustomAttribute('condition', $post['condition']);
      $_product->setCustomAttribute('date', date('m/d/Y h:i:s a', time()));
      $_product->setCustomAttribute('local_global', $local_global);

      // set the local/global attribute
      if(strpos($local_global, $local_id) !== FALSE){
        // local product
        $_product->setCustomAttribute('latitude', $post['latitude']);
        $_product->setCustomAttribute('longitude', $post['longitude']);
      }// end if setting local attribute

      // tempory location for product images
      $mediaDir = '/var/www/html/pub/media';

      // save uploaded images to the product gallery
      foreach($images as $key => $image)
      {
          // get temporary location of image and image extension
          $tmpPath = $_FILES['images']['tmp_name'][$key];

          if($tmpPath != '')
          {
            $extension = pathinfo($image, PATHINFO_EXTENSION);

            // new path for the image stored in the media directory
            $newPath = $mediaDir.$tmpPath.'.'.$extension;

            // move the uploaded image to the media directory
            move_uploaded_file($tmpPath, $newPath);

            // link the image to the product and upload it to the S3 bucket
            $_product->addImageToMediaGallery($newPath, array('image', 'small_image', 'thumbnail'), false, false);

            // remove the image from our file Filesystem
            unlink($newPath);
          }// end if we have a temp file path
      }// end foreach loop over images

      // save the product to the database
      $_product->save();

      // save a vendor product with the seller
      $objectManager->get('\Magento\Framework\Registry')->register('saved_product', $_product);
      $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->saveProduct(\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE);
      $this->_eventManager->dispatch('csmarketplace_vendor_new_product_creation', [
        'product' => $_product,
        'vendor_id' => $this->session->getVendorId()
      ]);

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute
}
