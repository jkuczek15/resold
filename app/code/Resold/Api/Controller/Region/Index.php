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
namespace Resold\Api\Controller\Region;

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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->regionFactory = $regionFactory;
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

      if(!isset($post['regionCode']) || $post['regionCode'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if region code is set

      if(!isset($post['countryId']) || $post['countryId'] == null){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if country ID is set

      $region_code = $post['regionCode'];
      $country_id = $post['countryId'];

      $region = $this->regionFactory->create();
      $regionId = $region->loadByCode($region_code, $country_id)->getId();

      return $this->resultJsonFactory->create()->setData(['regionId' => $regionId]);
    }// end function execute
}
