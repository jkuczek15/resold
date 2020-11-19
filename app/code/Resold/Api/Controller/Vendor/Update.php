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
      if(strpos($_SERVER['HTTP_HOST'], 'resold') !== false) {
        // production
        $mediaDir = 'ced/csmaketplace/vendor';
      } else {
        // local
        $mediaDir = '/var/www/html/pub/media/ced/csmaketplace/vendor';
      }// end if host contains resold

      // load the seller by the customer ID
      $vendor = $this->vendor->loadByCustomerId(isset($_POST['customerId']) ? $_POST['customerId'] : -1);
      if($vendor != null && isset($_FILES['profilePicture']) && $_FILES['profilePicture']['name'] != null){

        // we have an image
        $image = $_FILES['profilePicture'];

        // temp server image path
        $tmpPath = $image['tmp_name'];

        // type of image
        $type = $image['type'];
        switch($type){
          case 'image/jpeg':
            $extension = 'jpeg';
            break;
          case 'image/jpg':
            $extension = 'jpg';
            break;
          case 'image/png':
            $extension = 'png';
            break;
          default:
            $extension = 'png';
            break;
        }// end switch on type

        // in this case extension is already set
        $tmpPathExt = 'profile_picture'.$vendor->getId().'.'.$extension;

        // new path for the image stored in the media directory
        $newPath = $mediaDir.'/'.$tmpPathExt;

        // move the uploaded image to the media directory
        move_uploaded_file($tmpPath, $newPath);

        // save the vendor profile picture
        $vendor->addData([
          'profile_picture' => $newPath
        ]);

        // todo: not the correct way to save the image, Magento somehow saves to S3
        $vendor->save();
      }// end if we have an image

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData(['success' => 'Y', 'path' => $tmpPathExt]);
    }// end function execute
}
