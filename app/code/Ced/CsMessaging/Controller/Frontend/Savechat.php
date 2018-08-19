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
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager
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
        $this->date = $date;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
        //     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
    	  $data = $this->getRequest()->getPostValue();
        $subject=$this->getRequest()->getPost('email_subject');
        $message=$this->getRequest()->getPost('text_email');
        $receiver_email=$this->getRequest()->getPost('receiver_email');
        $receiver_id=$this->getRequest()->getPost('vendor_id');
        if($receiver_email=="") {
            $vendor_data=$this->getRequest()->getPost('vendor_data');
            if ($vendor_data == 0)
            {
              $receiver_email = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
            } else {
              $vendor=$this->_vendorFactory->create()->load($vendor_data);
              $receiver_email=$vendor->getEmail();
            }
            $receiver_id=$vendor_data;

        }

        $vendor=$this->_vendorFactory->create()->load($receiver_id);
        if ($this->getRequest()->getPost('vendor_data') == 0)
        {
          $receiver_name = $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } else {
          $receiver_name=$vendor->getName();
        }

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


                if(isset($data['sent_to_vendor'])) {
                      $data= array();
                      $data['receiver_email'] = $receiver_email;
                      $data['text'] = $message;
                      $data['vendor_name'] = $sender_name;
                      $data['receiver_name'] = $receiver_name;
                      $data['subject'] = $subject;
                      $this->_template  = 'send_cmail_to_vendor';
                      $this->inlineTranslation->suspend();
                      $this->_transportBuilder->setTemplateIdentifier($this->_template)
                            ->setTemplateOptions(
                                [
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => $this->_storeManager->getStore()->getId(),
                                ]
                            )
                            ->setTemplateVars($data)
                            ->setFrom([
                                'name' => $sender_name,
                                'email' => $sender_email
                                ])
                            ->addTo($receiver_email, $receiver_name);
                      try {
                        $transport = $this->_transportBuilder->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                      } catch (\Exception $e) {
                          throw new \Exception (__($e->getMessage()));
                      }

                }
                 /**
                     * Send the Response to admin
                     */
            }
            catch(\Exception $e){
                throw new \Exception (__($e->getMessage()));
            }
            $this->messageManager->addSuccessMessage(__('Your Query Has Been Sent.'));
             $this->_redirect('csmessaging/frontend/customercompose');
        }
        else
        {
          $this->messageManager->addErrorMessage(__('Please Specify Recipient.'));
          $this->_redirect('csmessaging/frontend/customercompose');
        }
    }

}
