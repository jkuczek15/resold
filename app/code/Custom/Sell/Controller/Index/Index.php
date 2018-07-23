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
          $_product->setSku(md5($post['name'] . date("l jS \of F Y h:i:s A")));
          $_product->setTypeId('simple');
          $_product->setAttributeSetId(4);
          $_product->setVisibility(4);
          $_product->setPrice($post['price']);
          $_product->setDescription($post['description']);
          $_product->setCategoryIds([$post['lowestcategory']]);
          $_product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
          $_product->setWebsiteIds(array(1));

          // TODO: set the product image
          // $_product->setImage('/testimg/test.jpg');
          // $_product->setSmallImage('/testimg/test.jpg');
          // $_product->setThumbnail('/testimg/test.jpg');

          $_product->setCustomAttribute('condition', $post['condition']);

          // save the product to the database
          $_product->save();

          // redirect to the product page
          return $resultRedirect->setPath($_product->getProductUrl());
        }else{
          // GET request
          return $this->resultPageFactory->create();
        }
    }
}
