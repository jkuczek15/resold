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
    \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
    \Magento\Customer\Model\Session $customerSession
  )
  {
      $this->session = $customerSession;
      $this->userContext = $userContext;
      $this->resultJsonFactory = $resultJsonFactory;
  }

	/**
	 * {@inheritdoc}
	 */
  public function createProduct($name, $price, $topCategory, $condition, $details, $localGlobal, $imagePaths, $latitude, $longitude)
	{
    $customerId = $this->userContext->getUserId();
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    ####################################
    // REQUEST AND USER VALIDATON
    ###################################
    // Ensure user is logged in
    if (!$this->session->isLoggedIn()) {
      return $this->resultJsonFactory->create()->setData(['error' => 'You must be logged in to sell items.']);
    }// end if user not logged in

    if ($customerId == null) {
      return $this->resultJsonFactory->create()->setData(['error' => 'You must be logged in to sell items.']);
    }// end if user not logged in

    ####################################
    // FORM VALIDATION
    ###################################
    $local_id = '231';

    // determine whether we need to skip price validation
    if(!is_numeric($price) || $price < 5){
      return $this->resultJsonFactory->create()->setData(['error' => 'Price must be an integer greater than 5.']);
    }// end if invalid price

    // location validation
    if(!is_numeric($latitude) || !is_numeric($longitude)){
      return $this->resultJsonFactory->create()->setData(['error' => 'Invalid location.']);
    }// end if latitude longitude not set

    // image validation
    if($imagePaths == ''){
      return $this->resultJsonFactory->create()->setData(['error' => 'At least one image is required.']);
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

    $vendorId = $this->session->getVendorId();

    var_dump($post);
    var_dump($vendorId);
    exit;
		return 'api POST return the $param ' . $param . ' with customer id: ' . $customerId;
	}
}
