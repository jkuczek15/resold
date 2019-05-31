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
use Ced\CsMarketplace\Model\VendorFactory;

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
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        VendorFactory $Vendor,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->vendor = $Vendor;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->customerRepository = $customerRepository;
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

      // determine whether we need to skip price validation
      $skip_price_validation = $product_id != null && substr($_product->getSku(), 0, 3) == 'amz';
      if(!$skip_price_validation && (!is_numeric($price) || $price < 5)){
        return $this->resultJsonFactory->create()->setData(['error' => 'Price must be an integer greater than 5.']);
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

      // check to make sure the user has authenticated with stripe
      // this means the vendor id should be non-null
      $vendorId = $this->session->getVendorId();
      $standalone = $this->_objectManager->create('Ced\CsStripePayment\Model\Standalone');
      $stripe_model = $standalone->load($vendorId, 'vendor_id')->getData();

      $stripe_connected = true;
      if(count($stripe_model) == 0){
        // check to see if connected to stripe
        // the user hasn't connected to stripe yet
        $stripe_connected = false;
        $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
        $customer = $this->session->getCustomer();

        try {
          // send an email to the user letting them know they need to connect to stripe
          $this->inlineTranslation->suspend();
          // send the customer an email telling them to connect with stripe
          $sender = [
            'name' => 'Resold',
            'email' => 'support@resold.us'
          ];

          $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          $transport = $this->_transportBuilder
            ->setTemplateIdentifier('connect_to_stripe_template') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions([
              'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
              'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
          ])
          ->setTemplateVars(['host' => $_SERVER['HTTP_HOST'], 'name' => $customer->getName() ])
          ->setFrom($sender)
          ->addTo($customer->getEmail())
          ->getTransport();

          $transport->sendMessage();
          $this->inlineTranslation->resume();
          // $this->messageManager->addWarning('Your items are not yet live. Connect your account with Stripe to start getting paid on Resold.');
        }
        catch(\Exception $e)
        {
          $this->inlineTranslation->resume();
        }// end try catch
      }// end if user hasn't connected to Stripe

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

      // Ensure user is a seller
      if($this->session->getVendorId() == null){
        // create a mew vendor/seller account
        $vendorModel = $this->vendor->create();
        $customer = $this->session->getCustomer();

        // change the user group
        $customer_id = $customer->getId();
        $customer = $this->customerRepository->getById($customer_id);
        $customer->setGroupId(5);
        $this->customerRepository->save($customer);

        try {
          $vendor = $vendorModel->setCustomer($customer)->register([
            'public_name' => $customer->getFirstname().' '.$customer->getLastname(),
            'shop_url' => uniqid()
          ]);
          $vendor->setGroup('general');
          if (!$vendor->getErrors()) {
              $vendor->save();
              $this->session->setVendorId($vendor->getId());
          } elseif ($vendor->getErrors()) {
              foreach ($vendor->getErrors() as $error) {
                  $this->session->addError($error);
              }
              $this->session->setFormData($vendor);
          } else {
              $this->session->addError(__('Your application has been denied'));
          }
        } catch (\Exception $e) {
            $this->helper->logException($e);
        }// end try-catch creating a new vendor account
      }// end if vendor id not set

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

      if(!$stripe_connected){
        return $this->resultJsonFactory->create()->setData(['stripe_redirect' => 'Y']);
      }// end if stripe not connected

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute
}
