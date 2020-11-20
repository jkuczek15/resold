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
use \Thai\S3\Model\MediaStorage\File\Storage\S3;

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
        S3 $storage,
        JsonFactory $resultJsonFactory
    )
    {
        $this->vendor = $vendor;
        $this->storage = $storage;
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
      $vendorDir = 'ced/csmaketplace/vendor'; 
      $mediaDir = '/var/www/html/pub/media/';

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
        $s3Path = $vendorDir.'/'.$tmpPathExt;
        $newPath = $mediaDir.$s3Path;

        // move the uploaded image to the media directory
        move_uploaded_file($tmpPath, $newPath);

        // save the uploaded photo to S3
        $this->storage->saveFile($s3Path);

        // save the vendor profile picture
        $vendor->addData([
          'profile_picture' => $s3Path
        ]);

        $vendor->save();
      }// end if we have an image

      // on success, return s3 image path
      return $this->resultJsonFactory->create()->setData(['success' => 'Y', 'path' => $s3Path]);
    }// end function execute
}
