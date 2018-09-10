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
namespace Custom\Api\Controller\Image;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class Get extends \Magento\Framework\App\Action\Action
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
      $result = [];
      // tempory location for product images
      if(isset($_GET['product_id']) && $_GET['product_id'] != null){
          $product_id = $_GET['product_id'];

          // retreive the seller's product
          $model = \Magento\Framework\App\ObjectManager::getInstance();
          $product = $model->create('Magento\Catalog\Model\Product')->load($product_id);

          // get the gallery images for this product
          $images = $product->getMediaGallery('images');

          $result = [];
          $count = 1;
          $mediaDir = '/pub/media/catalog/product';
          foreach($images as $image){
            $result[] = [
              'name' => '',
              'uuid' => $image['file'],
              'thumbnailUrl' => $mediaDir.$image['file']
            ];
          }// end foreach loop over gallery images

      }// end if product id set

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData($result);
    }// end function execute
}
