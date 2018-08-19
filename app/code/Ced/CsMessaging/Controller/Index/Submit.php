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
namespace Ced\CsMessaging\Controller\Index;

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
    protected $_transportBuilder;
    protected $_messagingFactory;
    protected $inlineTranslation;
    protected $_objectManager;

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


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $date;


    // protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {

       $this->storeManager = $storeManager;
       $this->_escaper = $escaper;
       $this->inlineTranslation = $inlineTranslation;
       $this->_transportBuilder = $transportBuilder;
       $this->_messagingFactory = $messagingFactory;
        $this->session = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->date = $date;
        $this->_storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }


    public function _getSession()
    {
        return $this->session;

    }
    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
            $vendor_pic="";
            $vendor=$this->_getSession()->getVendor();
            $vendor_id=$vendor['entity_id'];
            $vendor_message=$this->getRequest()->getParam('msg');
            $vendor_subject=$this->getRequest()->getParam('subject');
          	$isSentMail = $this->getRequest()->getParam('isMailsent');

            $sender_email=$vendor['email'];
            $sender_name=$vendor['name'];

            $reciever_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
            $receiver_name =$this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver name
            $date=$this->date->date('Y-m-d');
            $time=$this->date->date('H:i:s');
            $chat_collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', $vendor_id)->getLastItem()->getData();

            $date=$this->date->date('Y-m-d');//Mage::getModel('core/date')->date('Y-m-d');
            $time=$this->date->date('H:i:s');//Mage::getModel('core/date')->date('H:i:s');
            $chat_collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', $vendor_id)->getLastItem()->getData();
            //$chat_collection=Mage::getModel('csvendorchat/chat')->getCollection()->addFieldToFilter('sender_id',$vendor_id)->getLastItem()->getData();
        if(sizeof($chat_collection)==0) {
            $count=1;
        }
        else{
            $count=$chat_collection['vcount'];
            $count++;
        }

            $sender_id=$vendor['entity_id'];
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
                $model->save();

                /**
                     * Send Email also to Admin
                    */
                $enable=$this->_scopeConfig->getValue('ced_csmarketplace/csmessaging/active1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $enable=$this->_scopeConfig->getValue('ced_csmarketplace/csmessaging/active1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);//Mage::getStoreConfig('ced_csmarketplace/vendor_chat_group/emailchat');
                $data= array();
                $data['receiver_email'] = $reciever_email;
                $data['text'] = $vendor_message;
                $data['vendor_name'] = $sender_name;
                $data['subject'] = $vendor_subject;
                $this->_template  = 'send_customer_mail';
                if($isSentMail) {
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
