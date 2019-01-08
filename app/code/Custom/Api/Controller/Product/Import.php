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

      if($product_id != null){
        // product ID mode
        $command = 'php ./import_products.php -p ' . $product_id;
      }else{
        // search query mode
        $command = 'php ./import_products.php -s ' . $search_query;
      }// end if product ID not null

      // add the vendor ID as a parameter for our job
      $command .= ' '. $this->session->getVendorId();

      if($override_price != null){
        $command .= ' '.$override_price;
      }// end if override price parameter not null

      if($pages != null){
        $command .= ' '.$pages;
      }// end if page parameter not null

      // execute the command in the background
      $output = shell_exec('cd /var/www/html/jobs && ' . $command .' > /dev/null 2>/dev/null &');

      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute

}
