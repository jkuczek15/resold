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
      // FIXME: Before launch lockdown API using the code below
      // if (!$this->session->isLoggedIn()) {
      //   $resultRedirect = $this->resultRedirectFactory->create();
      //   return $resultRedirect->setPath('market/account/login');
      // }

      $post = $this->getRequest()->getPostValue();

      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'This request method is not supported.']);
      }

      // POST request
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $_product = $objectManager->create('\Magento\Catalog\Model\Product');

      // TODO: Add server side validation
      // Create a unique product ID and save product to database
      $sku = uniqid ("product-", true);
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

      // save the product to the database
      $_product->save();

      return $this->resultJsonFactory->create()->setData(['id' => $sku]);
    }// end function execute
}
