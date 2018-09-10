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

class Delete extends \Magento\Framework\App\Action\Action
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
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      // tempory location for product images
      $mediaDir = '/var/www/html/pub/media';

      if(isset($_POST['qquuid']) && $_POST['qquuid'] != null)
      {
          $tmpPathExt = $_POST['qquuid'];

          if(strpos($tmpPathExt, "/tmp") !== FALSE){
            // deleting a temporary file on the server
            unlink($mediaDir.$tmpPathExt);
          }else if(isset($_POST['product_id']) && $_POST['product_id'] != null){
            // deleting and unlinking permanent product gallery images
            $product_id = $_POST['product_id'];
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
            $imageProcessor = $objectManager->create('\Magento\Catalog\Model\Product\Gallery\Processor');
            $imageProcessor->removeImage($product, $tmpPathExt);
          }// end if temporary image directory

      }// end if temp file path is set

      $product->save();
      return $this->resultJsonFactory->create()->setData(['success' => 'Y', 'path' => $tmpPathExt]);
    }// end function execute
}
