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
namespace Resold\Api\Controller\Vendor;

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
        VendorFactory $Vendor
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->vendor = $Vendor;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $resultRedirect = $this->resultRedirectFactory->create();
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      if(!isset($post['customerId']) || $post['customerId'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if customer ID is set

      $vendorModel = $this->vendor->create();
      $vendor = $vendorModel->loadByCustomerId($post['customerId']);

      if($vendor) {
        return $this->resultJsonFactory->create()->setData(['vendorId' => $vendor->getId()]);
      } else {
        return $this->resultJsonFactory->create()->setData(['vendorId' => '-1']);
      }
    }// end function execute
}
