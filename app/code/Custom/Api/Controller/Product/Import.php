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

class Import extends \Magento\Framework\App\Action\Action
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
        return $this->resultJsonFactory->create()->setData(['error' => 'You must be logged in to import items.']);
      }// end if user not logged in

      // Ensure user is a seller
      if($this->session->getVendorId() == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'Your account must be connected to Stripe to import items.']);
      }// end if vendor id not set

      $email = $this->session->getCustomer()->getEmail();
      $valid_emails = ['joe.kuczek@gmail.com', 'joe@resold.us', 'justinspecht3@gmail.com', 'justin@resold.us', 'dunderwager@gmail.com'];
      if(!in_array($email, $valid_emails)){
        return $this->resultJsonFactory->create()->setData(['error' => 'You do not have access to import products.']);
      }// end if email not in list of valid emails

      // Set our time zone to Chicago
      date_default_timezone_set('America/Chicago');

      ####################################
      // RETREIVE PRODUCTS FROM AMAZON
      ###################################
      $search_query = isset($_POST['q']) ? $_POST['q'] : null;
      $pages = isset($_POST['pages']) ? $_POST['pages'] : 1;
      $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
      $override_price = isset($_POST['price']) ? $_POST['price'] : null;

      if($search_query == null && $product_id == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'Please provide a search query or product ID.']);
      }// end if no search query provided

      // initialize CURL parameters
      $page = 1;
      $products_imported = 0;
      $retailer = 'amazon';

      // retreive product data from ZINC
      $all_products = [];
      do {
        // loop while we have product search results
        if($search_query != null)
        {
          // retreive product data from API
          $result = $this->makeRequest('search?query='.$search_query.'&page='.$page.'&retailer='.$retailer);

          // decode the product data
          $product_result = json_decode($result, true);
          if(!isset($product_result['results']))
          {
            var_dump($product_result);
            exit;
          }// end if there was an error

          $products = $product_result['results'];
        }else{
          $products = [['product_id' => $product_id]];
        }// end if search query is not null

        foreach($products as $product)
        {
          // retreive product details
          $result = $this->makeRequest('products/'.$product['product_id'].'?retailer='.$retailer);

          // decode the product data
          $product_details = json_decode($result, true);

          ####################################
          // SAVE PRODUCT TO DATABASE
          ###################################
          // all amazon products have a sku beginning with 'amz-'
          $all_category_id = 105;
          $sku = 'amz-'.$product_details['product_id'];
          $price = sprintf("%.2f", $product_details['price'] / 100);

          if($override_price != null){
            $price = $override_price;
          }else if($price == 0){
            echo "Could not find a price for the specified product.";
            exit;
          }else{
            // round the price up and add our fees
            $price = ceil($price + ($price * 0.06) + 1);
          }// end if no price provided

          $product_description = '';
          if(isset($product_details['product_description']) && $product_details['product_description'] != null){
            $product_description = $product_details['product_description'];
          }else if(isset($product_details['feature_bullets']) && $product_details['feature_bullets'] != null){
            $product_description = implode ('<br/>', $product_details['feature_bullets']);
          }// end if product description is not null

          // create the Magento product
          $_product = $objectManager->create('Magento\Catalog\Model\Product');
          $_product->setSku($sku);
          $_product->setCreatedAt(strtotime('now'));
          $_product->setCustomAttribute('date', date('m/d/Y h:i:s a', time()));

          // map attributes from Amazon product
          $_product->setName($product_details['title']);
          $_product->setPrice($price);
          $_product->setDescription($product_description);
          $_product->setCustomAttribute('title_description', "Listed on Amazon");
          $_product->setCategoryIds($all_category_id);

          // default magento settings
          $_product->setTypeId('simple');
          $_product->setStoreId(1);
          $_product->setAttributeSetId(4);
          $_product->setVisibility(4);
          $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
          $_product->setWebsiteIds(array(1));
          $_product->setStockData(['qty' => 1, 'is_in_stock' => true]);

          // default Resold settings
          $_product->setCustomAttribute('condition', 227);
          $_product->setCustomAttribute('local_global', 232);

          ####################################
          // IMPORT PRODUCT IMAGES
          ###################################
          // tempory location for product images
          $image_paths = array_reverse($product_details['images']);
          $mediaDir = '/var/www/html/pub/media';

          // loop over all temporary images uploaded for this product
          foreach($image_paths as $key => $image_path)
          {
            // find the index of the last period in the full image URL
            $period_index = strrpos($image_path, '.');

            // get the image file extension
            $extension = substr($image_path, $period_index);

            // save the image temporarily
            $tmpFilePath = $mediaDir . '/tmp/' . $sku . '-' . $key . '.'.$extension;
            file_put_contents($tmpFilePath, file_get_contents($image_path));

            // save the image with the product
            $_product->addImageToMediaGallery($tmpFilePath, array('image', 'small_image', 'thumbnail'), false, false);

            // delete the image post-import
            unlink($tmpFilePath);
          }// end foreach loop over image paths

          // save the product to the database
          $_product->save();
          $products_imported++;

          // creating a new product and linking it to the seller
          // save a vendor product with the seller
          $objectManager->get('\Magento\Framework\Registry')->register('saved_product', $_product);
          $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->saveProduct(\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE);
          $this->_eventManager->dispatch('csmarketplace_vendor_new_product_creation', [
            'product' => $_product,
            'vendor_id' => $this->session->getVendorId()
          ]);
          $objectManager->get('\Magento\Framework\Registry')->unregister('saved_product');
        }// end foreach loop over retreived products

      }while($page++ < $pages);

      return $this->resultJsonFactory->create()->setData(['success' => 'Y', 'products' => $products_imported]);
    }// end function execute

    /**
     * Make CURL request
     *
     * @return requestResult
     */
    public function makeRequest($url)
    {
      // Zinc API client ID and base url
      $base_url = 'https://api.zinc.io/v1/';
      $encoded_client_id = 'NkNBNjcxMUQxMTE3MzZFQzgwMjU1QUQ4Og==';

      // CURL request to retreive product data
      $curl = curl_init();

      // setup additonal CURL options
      curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => $base_url.$url
      ));

      // set authorization header
      $headers = array(
          'Content-Type:application/json',
          'Authorization: Basic '.$encoded_client_id
      );
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

      // make CURL request
      $result = curl_exec($curl);

      // close CURL session
      curl_close ($curl);

      // sleep for a second
      sleep(1);

      return $result;
    }// end function makeRequest
}
