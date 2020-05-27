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
namespace Resold\Api\Controller\Image;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class Upload extends \Magento\Framework\App\Action\Action
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
        JsonFactory $resultJsonFactory
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      // tempory location for product images
      $mediaDir = '/var/www/html/pub/media';

      if(isset($_FILES['qqfile']) && $_FILES['qqfile']['name'] != null){
        // we have an image
        $image = $_FILES['qqfile'];

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

        if($tmpPath != '')
        {
            // temporary path with extension for image
            $tmpPathExt = $tmpPath.'.'.$extension;

            // new path for the image stored in the media directory
            $newPath = $mediaDir.$tmpPathExt;

            // move the uploaded image to the media directory
            move_uploaded_file($tmpPath, $newPath);
        }// end if valid tmp path

      }// end if we have an image

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData(['success' => 'Y', 'path' => $tmpPathExt, 'newUuid' => $tmpPathExt]);
    }// end function execute
}
