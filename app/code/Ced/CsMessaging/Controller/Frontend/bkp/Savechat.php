<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CsMessaging
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsMessaging\Controller\Frontend;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Savechat extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    
    public $_allowedResource = true;
    
    /**
 * @var Session 
*/
    protected $session;
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
     
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    protected $_messagingFactory;
    protected $_vendorFactory;
    
    protected $_transportBuilder;
    protected $inlineTranslation;
    protected $_escaper;
    protected $scopeConfig;
    protected $objectManager;
    protected $date;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
           $this->scopeConfig=$scopeConfig;
           $this->_escaper = $escaper;
          $this->inlineTranslation = $inlineTranslation;
          $this->_transportBuilder = $transportBuilder;
          $this->_messagingFactory = $messagingFactory;
          $this->_vendorFactory = $vendorFactory;
         $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->urlModel = $urlFactory;
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_moduleManager = $moduleManager;
        $this->_objectManager = $objectManager;
        $this->date = $date;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        //     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $subject=$this->getRequest()->getPost('email_subject');
        $message=$this->getRequest()->getPost('text_email');
        $receiver_email=$this->getRequest()->getPost('receiver_email');
        $receiver_id=$this->getRequest()->getPost('vendor_id');
        if($receiver_email=="") {
            $vendor_data=$this->getRequest()->getPost('vendor_data');
            $vendor=$this->_vendorFactory->create()->load($vendor_data);
            $receiver_email=$vendor->getEmail();
            $receiver_id=$vendor_data;
        }
        
        $vendor=$this->_vendorFactory->create()->load($receiver_id);
        $receiver_name=$vendor->getName();
        $customerData = $this->session->getCustomer();
        $sender_id= $customerData->getId();
        $sender_name=$customerData->getName();
        $sender_email=$customerData->getEmail();
        
        $date=$this->date->date('Y-m-d');//Mage::getModel('core/date')->date('Y-m-d');
        $time=$this->date->date('H:i:s');//Mage::getModel('core/date')->date('H:i:s');
        $chat_collection= $this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', $receiver_id)->getLastItem()->getData();
        
        if(sizeof($chat_collection)==0) {
            $count=1;
        }
        else{
            $count=$chat_collection['vcount'];
            $count++;
        }
        if($receiver_email!="") {
            try{
                $model=$this->_messagingFactory->create();
                $model->setData('subject', $subject);
                $model->setData('message', $message);
                $model->setData('sender_id', $sender_id);
                $model->setData("receiver_name", $receiver_name);
                $model->setData("receiver_email", $receiver_email);
                $model->setData("sender_email", $sender_email);
                $model->setData('date', $date);
                $model->setData('time', $time);
                $model->setData('vendor_id', $receiver_id);
                $model->setData('vcount', $count);
                $model->setData('postread', 'new');
                $model->setData('role', 'customer');
                $model->save();
                /**
                 * Send Email also to Vendor
                */
                $enable=$this->scopeConfig->getValue('ced_csmarketplace/csmessaging/active3', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                //$enable=1;//Mage::getStoreConfig('ced_csmarketplace/vendor_chat_group/allow_vendor_customer_email');
                $data= array();
                $data['receiver_email'] = $receiver_email;
                $data['text'] = $message;
                $data['vendor_name'] = $sender_name;
                $data['receiver_name'] = $receiver_name;
                $data['subject'] = $subject;
                //$emailTemplate  = Mage::getModel('core/email_template')
                //->loadDefault('send_cmail_to_vendor');
                /**
                 * Set Email information
                */
                if($enable=='1') {
                
                                  
                    try {
                        $this->_objectManager->get('Ced\CsMessaging\Helper\Data')->customertovendoremail($templateId, $storeId, $data, $sender_email, $reciever_email);
                    }
                    catch(\Exception $e)
                    {
                        echo $e->getMessage();
                    }
                        /* $emailTemplate->setTemplateSubject("Message from Vendor ".$sender_name);
                    $emailTemplate->setSenderName($sender_name);
                    $emailTemplate->setSenderEmail($sender_email);
                    $processedTemplate = $emailTemplate->getProcessedTemplate($data);
                    /**
                    * Send
                    */
                        //$emailTemplate->send($reciever_email,$receiver_name,$data); */
                }
                 /**
                     * Send the Response to admin
                     */    
            }
            catch(Exception $e){
                echo $e->getMessage();die;
            }
             $this->_redirect('csmessaging/frontend/customercompose');
        }
    }
    public function getVendorId()
    {
        return 'admin';
    }
}
