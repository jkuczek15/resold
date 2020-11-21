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
namespace Resold\Api\Model;

class ProductManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Magento\Authorization\Model\UserContextInterface $userContext,
    \Ced\CsMarketplace\Model\VendorFactory $Vendor,
    \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
    \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
    \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Framework\Event\ManagerInterface $eventManager,
    \Ced\CsMarketplace\Model\Vproducts $vendorProducts,
    \Ced\CsMarketplace\Model\Vendor $vendor
  )
  {
      $this->session = $customerSession;
      $this->userContext = $userContext;
      $this->vendor = $Vendor;
      $this->_transportBuilder = $transportBuilder;
      $this->inlineTranslation = $inlineTranslation;
      $this->_customerRepositoryInterface = $customerRepositoryInterface;
      $this->_eventManager = $eventManager;
      $this->vendorProducts = $vendorProducts;
      $this->vendor = $vendor;
  }

	/**
	 * {@inheritdoc}
	 */
  public function createProduct($name, $price, $topCategory, $condition, $details, $localGlobal, $imagePaths, $latitude, $longitude, $itemSize)
	{
    $customerId = $this->userContext->getUserId();
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    ####################################
    // REQUEST AND USER VALIDATON
    ###################################
    // Ensure user is logged in
    if ($customerId == null) {
      return [['error' => 'You must be logged in to sell items.']];
    }// end if user not logged in

    ####################################
    // FORM VALIDATION
    ###################################
    // determine whether we need to skip price validation
    if(!is_numeric($price) || $price < 5){
      return [['error' => 'Price must be an integer greater than 5.']];
    }// end if invalid price

    // location validation
    if(!is_numeric($latitude) || !is_numeric($longitude)){
      return [['error' => 'Invalid location.']];
    }// end if latitude longitude not set

    // item size validation
    if(!is_numeric($itemSize) || !is_numeric($itemSize)){
      return [['error' => 'Invalid item size.']];
    }// end if latitude longitude not set

    // image validation
    if($imagePaths == ''){
      return [['error' => 'At least one image is required.']];
    }// end if no images uploaded

    ####################################
    // SAVE PRODUCT TO DATABASE
    ###################################
    // POST request
    $all_category_id = 105;

    // Clean up the title and title description
    $name = ucwords(strtolower($name));

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

    // set product attributes
    $_product->setName($name);
    $_product->setTypeId('simple');
    $_product->setStoreId(1);
    $_product->setAttributeSetId(4);
    $_product->setVisibility(4);
    $_product->setPrice($price);
    $_product->setDescription(nl2br($details));
    $_product->setCategoryIds([$topCategory, $all_category_id]);
    $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
    $_product->setWebsiteIds(array(1));
    $_product->setStockData(['qty' => 1, 'is_in_stock' => true]);
    $_product->setCustomAttribute('condition', $condition);
    $_product->setCustomAttribute('local_global', $localGlobal);
    $_product->setCustomAttribute('latitude', $latitude);
    $_product->setCustomAttribute('longitude', $longitude);
    $_product->setCustomAttribute('location', $latitude.','.$longitude);
    $_product->setCustomAttribute('item_size', $itemSize);

    // tempory location for product images
    $mediaDir = '/var/www/html/pub/media';
    $image_types = ['image', 'small_image', 'thumbnail'];

    // create the base/primary image
    $primary_path = $mediaDir.$imagePaths[0];
    if(strpos($primary_path, "/tmp") !== FALSE || strpos($primary_path, "/craigslist") !== FALSE)
    {
      if(file_exists($primary_path))
      {
        // uploading a new file
        $_product->addImageToMediaGallery($primary_path, $image_types, false, false);
        unset($imagePaths[0]);
        unlink($primary_path);
      }// end if file exists
    }// end if uploading a new file

    // loop over all temporary images uploaded for this product
    foreach($imagePaths as $count => $imagePath)
    {
      // image will be given a new path once linked to the product
      $path = $mediaDir.$imagePath;
      if(strpos($path, "/tmp") !== FALSE || strpos($path, "/craigslist") !== FALSE)
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

    // load the vendor
    $vendorModel = $this->vendor->create();
    $vendor = $vendorModel->loadByCustomerId($customerId);
    $vendorId = $vendor->getId();
    $this->session->setVendorId($vendorId);

    $standalone = $objectManager->create('Ced\CsStripePayment\Model\Standalone');
    $stripe_model = $standalone->load($vendorId, 'vendor_id')->getData();

    if(count($stripe_model) == 0){
      // check to see if connected to stripe
      // the user hasn't connected to stripe yet
      $customer = $this->_customerRepositoryInterface->getById($customerId);

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
      }
      catch(\Exception $e)
      {
        $this->inlineTranslation->resume();
      }// end try catch
    }// end if user hasn't connected to Stripe

    // creating a new product and linking it to the seller
    // save a vendor product with the seller
    $objectManager->get('\Magento\Framework\Registry')->register('saved_product', $_product);
    $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->saveProduct(\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE);
    $this->_eventManager->dispatch('csmarketplace_vendor_new_product_creation', [
      'product' => $_product,
      'vendor_id' => $vendorId
    ]);

    return [['success' => 'Y', 'productId' => $_product->getId()]];
  }// end function createProduct

	/**
	 * {@inheritdoc}
	 */
  public function getProduct($productId)
	{
    // get the logged in customer's id
    $customerId = $this->userContext->getUserId();

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    // get the product
    $productsResult = $objectManager->create('Magento\Catalog\Model\Product')->getCollection()
      ->addAttributeToSelect($objectManager->create('Magento\Catalog\Model\Config')->getProductAttributes())
      ->addAttributeToSelect('latitude')
      ->addAttributeToSelect('longitude')
      ->addAttributeToSelect('description')
      ->addAttributeToSelect('title_description')
      ->addAttributeToSelect('local_global')
      ->addAttributeToSelect('charge_id')
      ->addAttributeToSelect('delivery_id')
      ->addAttributeToSelect('condition')
      ->addAttributeToFilter('entity_id', $productId)
      ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

    foreach($productsResult as $product) {
      $titleDescription = $product->getCustomAttribute('title_description');
      $localGlobal = $product->getCustomAttribute('local_global');
      $condition = $product->getCustomAttribute('condition');
      $latitude = $product->getCustomAttribute('latitude');
      $longitude = $product->getCustomAttribute('longitude');
      $chargeId = $product->getCustomAttribute('charge_id');
      $deliveryId = $product->getCustomAttribute('delivery_id');
      $categoryIds = $product->getCategoryIds();

      return [[
        'id' => $product->getId(),
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
        'delivery_id' => $deliveryId ? $deliveryId->getValue() : null,
      ]];
    }// end foreach over products
    return [['error' => 'Could not find product.']];
  }// end function getProduct

	/**
	 * {@inheritdoc}
	 */
  public function setDelivery($productId, $deliveryId)
	{
    // set the postmates delivery ID on the product
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
    $product->setCustomAttribute('delivery_id', $deliveryId);
    $product->save();
    return 1;
  }// end function setDeliveryId

	/**
	 * {@inheritdoc}
	 */
  public function setPrice($productId, $newPrice)
	{
    // set the postmates delivery ID on the product
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
    $product->setPrice($newPrice);
    $product->save();
    return 1;
  }// end function setDeliveryId

	/**
	 * {@inheritdoc}
	 */
  public function isProductMine($productId)
	{
    // get the logged in customer's id
    $vendor = $this->vendor->loadByCustomerId($this->userContext->getUserId());
    $vendorId = $this->vendorProducts->getVendorIdByProduct($productId);
    return $vendor->getId() == $vendorId;
  }// end function setDeliveryId
}
