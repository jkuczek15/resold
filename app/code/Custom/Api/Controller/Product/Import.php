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

      // Set our time zone to Chicago
      date_default_timezone_set('America/Chicago');

      ####################################
      // RETREIVE PRODUCTS FROM AMAZON
      ###################################
      $search_query = isset($_GET['q']) ? $_GET['q'] : null;
      $pages = isset($_GET['pages']) ? $_GET['pages'] : 1;
      $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

      if($search_query == null && $product_id == null)
      {
        echo "Please provide a search query (q parameter) or an item number (product_id parameter).<br/><br/>";
        echo "Examples: <ul><li>/api/product/import?q=laptop</li><li>/api/product/import?product_id=B07KCM4TCS</li></ul>";
        echo "Optionally provide a number of pages to import, example: <ul><li>/api/product/import?q=laptop&pages=5";
        exit;
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
          $price = ceil($price + ($price * 0.06) + 1);

          $product_description = '';
          if($product_details['product_description'] != null)
          {
            $product_description = $product_details['product_description'];
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

      // on success, redirect user to their listing page
      echo "Successfully imported $products_imported product(s).";
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
