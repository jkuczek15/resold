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

class Details extends \Magento\Framework\App\Action\Action
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

      if(!isset($post['vendorId']) || $post['vendorId'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if vendor ID is set

      if($post['vendorId'] == '-1') {
        return $this->resultJsonFactory->create()->setData(['error' => 'You have not listed any items for sale.']);
      }// end if vendor Id == -1

      // get vendor data
      $vendor = $this->vendor->create()->load($post['vendorId']);
      $vendorAttributes = $vendor->getVendorAttributes();

      $result = [
        'id' => $vendor->getId(),
        'name' => $vendor->getName(),
        'about' => trim($vendor->getAbout()),
        'profilePicture' => $vendor->getProfilePicture()
      ];
      var_dump($result);
      exit;

      return $this->resultJsonFactory->create()->setData([]);
    }// end function execute
}
