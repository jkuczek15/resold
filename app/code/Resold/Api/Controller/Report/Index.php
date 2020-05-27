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
namespace Resold\Api\Controller\Report;

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
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'report/email/email_template';

    /**
     * Enabled config path
     */
    const XML_PATH_ENABLED = 'contact/contact/enabled';

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
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
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

      // Ensure POST request
      $post = $this->getRequest()->getPostValue();
      if(empty($post)){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      if(!isset($post['product_id']) || $post['product_id'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if product id is set

      if(!isset($post['comment']) || $post['comment'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if product id is set

      // retreive the seller's product data
      $product_id = $post['product_id'];
      $_product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);

      try {
          $postObject = new \Magento\Framework\DataObject();
          $error = false;
          if (!\Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
              $error = true;
          }
          if (!\Zend_Validate::is(trim($post['product_id']), 'NotEmpty')) {
              $error = true;
          }
          if ($error) {
              throw new \Exception();
          }

          $post['item'] = $_product->getName();
          $post['productUrl'] = $_product->getProductUrl();

          $postObject->setData($post);
          $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
          $transport = $this->_transportBuilder
              ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
              ->setTemplateOptions(
                  [
                      'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                      'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                  ]
              )
              ->setTemplateVars(['data' => $postObject])
              ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
              ->addTo($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope))
              ->getTransport();

          $transport->sendMessage();
          $this->messageManager->addSuccess(
              __('Thank you for reporting this listing.')
          );
          return $resultRedirect->setPath($_product->getProductUrl());
      } catch (\Exception $e) {
          var_dump($e->getMessage());
          exit;
          $this->messageManager->addError(
              __('We can\'t process your request right now. Sorry, that\'s all we know.')
          );
          return $resultRedirect->setPath($_product->getProductUrl());
      }

      return $resultRedirect->setPath($_product->getProductUrl());
    }// end function execute
}
