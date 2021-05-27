<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMessaging
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsMessaging\Cron;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Ced\CsMessaging\Model\Messaging;
 
class SendMessageToCustomer {
 
    protected $_logger;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Messaging $Messaging,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->_vendorFactory = $vendorFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->_messagingFactory = $messagingFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->date = $date;
        $this->_storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messaging = $Messaging;
        $this->_logger = $logger;
    }
    
    /*
     * running cron job to send mails to the customers 
     */
    public function execute() {

     /*   $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customermailsender.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info("send mail to customer");*/

        $messagingData = $this->messaging->getCollection()
                                         ->addFieldToFilter('is_mail_sent','0')
                                         ->addFieldToFilter('role','admin')
                                         ->addFieldToFilter('send_mail','1')
                                         ->addFieldToFilter('send_to','customer');
        $messagingData->getSelect()->limit(10);
        $sender_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
        $sender_name = $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_template = 'send_mail_to_vendor';

        if (count($messagingData))
        {
           foreach ($messagingData as $data)
           {
            $objectManager = \Magento\Framework\App\objectManager::getInstance();
            $messagingModel = $objectManager->create('Ced\CsMessaging\Model\Messaging')->load($data->getChatId());
            $messagingModel->setIsMailSent('1');
            $messagingModel->save();
            $vendor_email = $data->getReceiverEmail();
            $vendor_name = $data->getReceiverName();
            $message = $data->getMessage();
            $subject = $data->getSubject();
            $sendData = [];
            $sendData['receiver_email'] = $vendor_email;
            $sendData['text'] = $message;
            $sendData['receiver_name'] = $vendor_name;
            $sendData['sender_name'] = $sender_name;
            $sendData['subject'] = $subject;
            $this->inlineTranslation->suspend();
            $this->_transportBuilder->setTemplateIdentifier($this->_template)
                  ->setTemplateOptions(
                      [
                          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                          'store' => $this->_storeManager->getStore()->getId(),
                      ]
                  )
                  ->setTemplateVars($sendData)
                  ->setFrom([
                      'name' => $sender_name,
                      'email' => $sender_email
                  ])
                  ->addTo($vendor_email, $vendor_name);

              try {
                  $transport = $this->_transportBuilder->getTransport();
                  $transport->sendMessage();
                  $this->inlineTranslation->resume();
              } catch (\Exception $e) {
                $this->_logger->debug('Error Message While Sending Mail To Customers : '.$e->getMessage());
              }
          }
        }
        $this->_logger->debug('Successfully mail sent to customers');
        return $this;
    }
}