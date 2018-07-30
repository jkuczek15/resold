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
      $resultRedirect = $this->resultRedirectFactory->create();
      if (!$this->session->isLoggedIn()) {
        return $resultRedirect->setPath('customer/account/login');
      }

      $post = $this->getRequest()->getPostValue();

      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'This request method is not supported.']);
      }

      // POST request
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $_product = $objectManager->create('\Magento\Catalog\Model\Product');

      // TODO: Add server side validation for raw data
      // Create a unique product ID and save product to database
      $sku = uniqid("product-", true);
      $_product = $objectManager->create('Magento\Catalog\Model\Product');
      $_product->setName($post['name']);
      $_product->setSku($sku);
      $_product->setTypeId('simple');
      $_product->setAttributeSetId(4);
      $_product->setVisibility(4);
      $_product->setPrice($post['price']);
      $_product->setDescription($post['description']);
      $_product->setCategoryIds([$post['lowestcategory']]);
      $_product->setCreatedAt(strtotime('now'));
      $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
      $_product->setWebsiteIds(array(1));
      $_product->setCustomAttribute('condition', $post['condition']);
      $_product->setStockData(['qty' => 1, 'is_in_stock' => true]);

      // TODO: Add service side validation for images
      // tempory location for product images
      $mediaDir = '/var/www/html/pub/media';
      $images = $_FILES['images']['name'];

      foreach($images as $key => $image)
      {
          // get temporary location of image and image extension
          $tmpPath = $_FILES['images']['tmp_name'][$key];

          if($tmpPath != '')
          {
            $extension = pathinfo($image, PATHINFO_EXTENSION);

            // new path for the image stored in the media directory
            $newPath = $mediaDir.$tmpPath.'.'.$extension;

            // move the uploaded image to the media directory
            rename($tmpPath, $newPath);

            // link the image to the product and upload it to the S3 bucket
            $_product->addImageToMediaGallery($newPath, array('image', 'small_image', 'thumbnail'), false, false);

            // remove the image from our file Filesystem
            unlink($newPath);
          }// end if we have a temp file path
      }// end foreach loop over images

      // save the product to the database
      $_product->save();

      return $resultRedirect->setPath('customer/account');
    }// end function execute
}
