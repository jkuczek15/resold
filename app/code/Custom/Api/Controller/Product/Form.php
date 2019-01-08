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
use Magento\Framework\View\Result\PageFactory;

class Form extends \Magento\Framework\App\Action\Action
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
        PageFactory $resultPageFactory
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->resultPageFactory = $resultPageFactory;
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
        return $this->resultJsonFactory->create()->setData(['error' => 'You do not have access to view this form.']);
      }// end if email not in list of valid emails

      return $this->resultPageFactory->create();
    }// end function execute
}
