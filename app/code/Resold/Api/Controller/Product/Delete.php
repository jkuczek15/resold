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
        JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\Registry $registry
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $this->_registry->register('isSecureArea', true);
      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post) || !isset($post['product_id'])){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      $model = \Magento\Framework\App\ObjectManager::getInstance();
      $product_id = $post['product_id'];
      $product = $this->_productRepositoryInterface->getById($product_id);
      $vendorId = $this->session->getVendorId();
      $vendorProduct = $model->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('sku', $product->getSku())->addFieldToFilter('check_status',['nin'=>3])->getFirstItem();

      // validation, make sure this product was created by the signed in user
      if($vendorProduct->getVendorId() !== $vendorId){
        return $this->resultJsonFactory->create()->setData(['error' => 'You do not have access to delete this listing,']);
      }// end if this isn't the seller's product

      $this->_productRepositoryInterface->delete($product);

      // on success, redirect user to their listing page
      $this->messageManager->addSuccess(__("Successfully deleted your listing."));
      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute
}
