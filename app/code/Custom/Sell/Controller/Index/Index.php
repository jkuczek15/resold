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
namespace Custom\Sell\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Ced\CsMarketplace\Helper\Data;
use Magento\Framework\Module\Manager;


class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public $helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        Manager $moduleManager,
        Data $datahelper
    )
    {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $datahelper;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->session->isLoggedIn()) {
            return $resultRedirect->setPath('market/account/login');
        }

        // retreive the POST data
        $post = $this->getRequest()->getPostValue();

        if(!empty($post)){
          // POST request
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $_product = $objectManager->create('\Magento\Catalog\Model\Product');

          // set product attributes
          $_product = $objectManager->create('Magento\Catalog\Model\Product');
          $_product->setName($post['name']);
          $_product->setSku($post['name']);
          $_product->setTypeId('simple');
          $_product->setAttributeSetId(4);
          $_product->setVisibility(4);
          $_product->setPrice(array($post['price']));
          $_product->setCategoryIds([
            $post['lowestcategory']
        ]); // here you are

          // set the product image
          // $_product->setImage('/testimg/test.jpg');
          // $_product->setSmallImage('/testimg/test.jpg');
          // $_product->setThumbnail('/testimg/test.jpg');

          $_product->setStockData(array(
            'use_config_manage_stock' => 0, // Use config settings' checkbox
            'manage_stock' => 1, // manage stock
            'min_sale_qty' => 1, // Minimum Qty Allowed in Shopping Cart
            'max_sale_qty' => 1, // Maximum Qty Allowed in Shopping Cart
            'is_in_stock' => 1, // Stock Availability
            'qty' => 1 //qty
            )
          );

          $_product->save();

          var_dump($post);
        }else{
          // GET request
          return $this->resultPageFactory->create();
        }
    }
}
