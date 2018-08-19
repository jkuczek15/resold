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
namespace Ced\CsMessaging\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Submit extends \Ced\CsMarketplace\Controller\Vendor
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
    protected $_objectManager;
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
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
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
          $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }

    /**
     *
     */
    public function execute()
    {

        $vendor=$this->_getSession()->getVendor();
        $vendor=$this->_getSession()->getVendor();
        $vendor_id=$vendor['entity_id'];
        //print_r($vendor_id);die;
        $reciever_email=$this->getRequest()->getParam('receiver');
        $sent_cust_mail=$this->getRequest()->getPost('sent_cust_mail');
        $vendor_message=$this->getRequest()->getParam('msg');
        $vendor_subject=$this->getRequest()->getParam('subject');

        $isSendMail=$this->getRequest()->getParam('isMailsent');
        //$cus
        $reciever = $this->_objectManager->get('Magento\Customer\Model\Customer')->setWebsiteId(1)->loadByEmail($reciever_email);
        $reviever_name = $reciever->getFirstname().' '.$reciever->getLastname();
        $receiver_name=$reviever_name;
        $sender_email=$vendor['email'];
        $sender_name=$vendor['name'];
        if(!$this->getRequest()->getPost('sent_cust_mail')) {
            $sent_cust_mail=0;
        }
        $date=$this->date->date('Y-m-d');
        $time=$this->date->date('H:i:s');
        $chat_collection=$this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', $vendor_id)->getLastItem()->getData();
        if(sizeof($chat_collection)==0) {
            $count=1;
        }
        else{
            $count=$chat_collection['vcount'];
            $count++;
        }
        $sender_id=$vendor['entity_id'];
        if($reciever_email=="") {
            $this->messageManager->addErrorMessage(__('Please Specify Recipient.'));
            $this->_redirect('csmessaging/customer/customer');
        }
        else{

            if($sender_email!="") {
                try{
                    $model=$this->_messagingFactory->create();
                    $model->setData('subject', $vendor_subject);
                    $model->setData('message', $vendor_message);
                    $model->setData('sender_id', $sender_id);
                    $model->setData("receiver_name", $receiver_name);
                    $model->setData("receiver_email", $reciever_email);
                    $model->setData("sender_email", $sender_email);
                    $model->setData('date', $date);
                    $model->setData('time', $time);
                    $model->setData('vendor_id', $sender_id);
                    $model->setData('vcount', $count);
                    $model->setData('postread', 'new');
                    $model->setData('role', 'vendor');
                    $model->setData('send_to', 'customer');
                    $model->save();
                    $enable=$this->scopeConfig->getValue('ced_csmarketplace/csmessaging/active1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    if($isSendMail) {

                        $data= array();
                        $receiver_name="Customer";
                        $data['receiver_email'] = $reciever_email;
                        $data['text'] = $vendor_message;
                        $data['vendor_name'] = $sender_name;
                        $data['receiver_name'] = $receiver_name;
                        $data['subject'] = $vendor_subject;
                        $this->_template  = 'send_vmail_to_customer';

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
                              ->addTo($reciever_email, $receiver_name);
                        try {
                          $transport = $this->_transportBuilder->getTransport();
                          $transport->sendMessage();
                          $this->inlineTranslation->resume();
                        } catch (\Exception $e) {
                            throw new \Exception (__($e->getMessage()));
                        }

                    }

                }
                catch(\Exception $e){
                    throw new \Exception (__($e->getMessage()));
                }

            }
            $message['msg'] = 'success';
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($message);
        }
    }




    public function _getSession()
    {
        return $this->session;
    }
}
