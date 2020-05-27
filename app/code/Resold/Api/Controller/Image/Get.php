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
use Aws\S3\S3Client;

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
        $this->s3 = S3Client::factory(array(
          'profile' => 'default',
          'region' => 'us-west-2',
          'version' => '2006-03-01'
        ));
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
              'name' => 'Image '.$count++,
              'uuid' => $image['file'],
              'thumbnailUrl' => $mediaDir.$image['file']
            ];
          }// end foreach loop over gallery images

      }else if(isset($_GET['post_count']) && $_GET['post_count'] !== null){

        $base_url = 'https://craigslist-photos-resold.s3-us-west-2.amazonaws.com/';
        $post_count = $_GET['post_count'];

        $image_keys = $this->s3->getIterator('ListObjects', array(
          'Bucket' => 'craigslist-photos-resold',
          'Prefix' => 'post-'.$post_count.'/'
        ));

        foreach ($image_keys as $count => $image) {
          $image_key = $image['Key'];
          $result[] = [
              'name' => 'Image '.$count,
              'uuid' => $base_url.$image_key,
              'thumbnailUrl' => $base_url.$image_key
          ];
        }
      }// end if product id set

      // on success, redirect user to their listing page
      return $this->resultJsonFactory->create()->setData($result);
    }// end function execute
}
