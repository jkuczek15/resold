<?php

class ImportProducts extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface
{
    public function launch()
    {
        $objectManager = $this->_objectManager;

        // Set our time zone to Chicago
        date_default_timezone_set('America/Chicago');

        $search_query = null;
        $product_id = null;
        $pages = isset($_SERVER['pages']) ? $_SERVER['pages'] : 1;
        $override_price = isset($_SERVER['price']) ? $_SERVER['price'] : null;
        $switch = $_SERVER['switch'];

        if($switch == '-s'){
          // search query mode
          $search_query = $_SERVER['value'];
        }else if($switch == '-p'){
          // product ID mode
          $product_id = $_SERVER['value'];
        }// end if search query mode

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
              // find the final price
							$order_data = [
								'retailer' => 'amazon',
                'max_price' => 0,
								'products' => [[
									'product_id' => $product_id,
									'quantity' => 1
								]],
								'shipping_address' => [
									'first_name' => '<your first name>',
									'last_name' => '<your last name>',
									'address_line1' => '<your address>',
									'address_line2' => '',
									'zip_code' => '<your zip code>',
									'city' => '<your city>',
									'state' => '<your state>',
									'country' => '<your country>',
									'phone_number' => '<your phone number>'
								],
								'shipping_method' => 'cheapest',
								'payment_method' => [
									'name_on_card' => '<your name>',
									'number' => '<your card number>',
									'security_code' => '<your security code>',
									'expiration_month' => '<your expiration month>',
									'expiration_year' => '<your expiration year>'
								],
								'billing_address' => [
									'first_name' => '<your first name>',
									'last_name' => '<your last name>',
									'address_line1' => '<your address>',
									'address_line2' => '',
									'zip_code' => '<your zip code>',
									'city' => '<your city>',
									'state' => '<your state>',
									'country' => '<your country>',
									'phone_number' => '<your phone number>'
								],
								'retailer_credentials' => [
									'email' => '<your email>',
									'password' => '<your password>'
								]
							];

              // place the fake order to get the final price
              $result = $this->makeRequest('orders', $order_data);
              $result = json_decode($result, true);

              // wait for the order status response
              $request_id = $result['request_id'];
              $order_processed = false;
              do {
                // check the order status until completed
                $result = $this->makeRequest('orders/'.$request_id);
                $result = json_decode($result, true);

                if(isset($result['code']) && $result['code'] == 'request_processing'){
                  sleep(30);
                  continue;
                }// end if request still processing

                $price = $result['data']['price_components']['total'];
                $price = sprintf("%.2f", $price / 100);
                $order_processed = true;
              }while(!$order_processed);
            }// end if no price provided

            // price upcharge
            $price = ceil($price + ($price * 0.1) + 1);

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
            $_product->setCustomAttribute('condition', 235);
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
            $_product->setVendorId($_SERVER['vendor_id']);
            $_product->save();
            $products_imported++;

            // creating a new product and linking it to the seller
            // save a vendor product with the seller
            $objectManager->get('\Magento\Framework\Registry')->register('saved_product', $_product);
            $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->saveProduct(\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE, $_SERVER['vendor_id']);
            $this->_eventManager->dispatch('csmarketplace_vendor_new_product_creation', [
              'product' => $_product,
              'vendor_id' => $_SERVER['vendor_id']
            ]);
            $objectManager->get('\Magento\Framework\Registry')->unregister('saved_product');
          }// end foreach loop over retreived products

        }while($page++ < $pages);

        // the method must end with this line
        return $this->_response;
    }// end function launch

    /**
     * Make CURL request
     *
     * @return requestResult
     */
    public function makeRequest($url, $data = null)
    {
      // Zinc API client ID and base url
      $base_url = 'https://api.zinc.io/v1/';
      $encoded_client_id = '<your ZINC client ID>';

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

      if($data != null){
				$payload = json_encode($data);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
      }// end if data passed for POST

      // make CURL request
      $result = curl_exec($curl);

      // close CURL session
      curl_close ($curl);

      // sleep for a second
      sleep(1);

      return $result;
    }// end function makeRequest

    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }// end function catchException

}// end class ImportProducts
