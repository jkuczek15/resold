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

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Ced\CsMarketplace\Model\Vendor;

class Update extends \Magento\Framework\App\Action\Action
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
        Vendor $vendor,
        JsonFactory $resultJsonFactory
    )
    {
        $this->vendor = $vendor;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Update the vendor
     *
     * @return void
     */
    public function execute()
    {
      // load the seller by the customer ID
      $vendor = $this->vendor->loadByCustomerId(isset($_POST['customerId']) ? $_POST['customerId'] : -1);
      $newPath = '';
      if($vendor != null && isset($_FILES['profilePicture']) && $_FILES['profilePicture']['name'] != null){

        // we have an image
        $image = $_FILES['profilePicture'];

        // temp server image path
        $tmpPath = $image['tmp_name'];

        // move the uploaded image to the media directory
        $newPath ='vendor'.$tmpPath; 

        move_uploaded_file($tmpPath, $newPath);

        // save the vendor profile picture
        $vendor->addData([
          'profile_picture' => $newPath
        ]);

        $vendor->save();
      }// end if we have an image

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData(['success' => 'Y', 'path' => $newPath]);
    }// end function execute
}
