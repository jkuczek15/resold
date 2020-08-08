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
namespace Resold\Api\Controller\Product;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use Ced\CsMarketplace\Model\VendorFactory;

class Customer extends \Magento\Framework\App\Action\Action
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
        JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        VendorFactory $Vendor
        // \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->vendor = $Vendor;
        // $this->_productRepositoryInterface = $productRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Return customer id for a product
     *
     * @return void
     */
    public function execute()
    {
      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post) || !isset($post['productId'])){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      $model = \Magento\Framework\App\ObjectManager::getInstance();
      $productId = $post['productId'];
      $vendorId = $model->create('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($productId);

      $vendorModel = $this->vendor->create();
      $vendor = $vendorModel->load($vendorId);

      return $this->resultJsonFactory->create()->setData(['customerId' => $vendor->getCustomerId()]);
    }// end function execute
}
