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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
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
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
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
        date_default_timezone_set("America/Chicago");
        // get request data
    	  $data = $this->getRequest()->getPostValue();
        $subject = $this->getRequest()->getPost('email_subject');
        $message = $this->getRequest()->getPost('text_email');
        $receiver_id = $this->getRequest()->getPost('vendor_id');
        $is_offer = $this->getRequest()->getPost('is_offer');
        $offer_price = $this->getRequest()->getPost('offer_price');
        $reply = $this->getRequest()->getPost('reply');
        $product_id = $this->getRequest()->getPost('product_id');
        $seller_cust_id = $this->getRequest()->getPost('seller_cust_id');

        if($reply){
            $vendor = $this->_customerRepositoryInterface->getById($receiver_id);
            $receiver_email = $vendor->getEmail();
            $receiver_name = $vendor->getFirstName() . " " . $vendor->getLastName();
        }else{
          // get current seller data
          $vendor = $this->_vendorFactory->create()->load($receiver_id);
          $receiver_email = $vendor->getEmail();
          $receiver_name = $vendor->getName();
        }

        if($seller_cust_id != null){
          $receiver_id = $seller_cust_id;
        }

        // get current customer data
        $customerData = $this->session->getCustomer();
        $sender_id = $customerData->getId();
        $sender_name = $customerData->getName();
        $sender_email = $customerData->getEmail();

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
            try {
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
                $model->setData('product_id', $product_id);
                if($offer_price != 0){
                  $model->setData('offer_price', $offer_price);
                }// end if offer price non zero
                $model->save();

                // send the email
                $data= array();
                $data['receiver_email'] = $receiver_email;

                $data['text'] = $message;
                $data['vendor_name'] = $sender_name;
                $data['receiver_name'] = $receiver_name;
                $data['subject'] = $subject;
                $data['encoded_subject'] = urlencode($subject);
                $data['sender_name'] = $sender_name;
                $data['is_offer'] = $is_offer;
                $data['offer_price'] = $offer_price;
                $data['product_url'] = $_SERVER['HTTP_REFERER'];
                $data['host'] = $_SERVER['HTTP_HOST'];

                $data['vendor_id'] = $receiver_id;
                $data['sender_id'] = $sender_id;
                $data['product_id'] = $product_id;

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
            catch(\Exception $e){
                throw new \Exception (__($e->getMessage()));
            }
            if($is_offer){
              $this->messageManager->addSuccessMessage(__('Your offer has been sent.'));
            }else{
              $this->messageManager->addSuccessMessage(__('Your message has been sent.'));
            }
            if($reply){
              return $this->_redirect('csmessaging/frontend/sent/');
            }
        }
        else
        {
          $this->messageManager->addErrorMessage(__('Please Specify Recipient.'));
          echo 'error';
        }
    }

}
