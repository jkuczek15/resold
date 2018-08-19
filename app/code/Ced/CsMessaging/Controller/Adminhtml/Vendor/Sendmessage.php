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

namespace Ced\CsMessaging\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Ced\CsMessaging\Model\Messaging;

class Sendmessage extends Action
{
    protected $resultPageFactory;

    protected $_transportBuilder;
    protected $_messagingFactory;
    protected $inlineTranslation;
    protected $_vendorFactory;
    protected $_objectManager;
    protected $date;
    protected $_storeManager;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
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

    }


    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        $admin_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
        $receiver_name = $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver name
        $vendor_id = $this->getRequest()->getParam('receiver');
        $sent_mail = $this->getRequest()->getPost('isMailsent');
        if (!$this->getRequest()->getPost('isMailsent')) {
            $sent_mail = 0;
        }
        $vendor = $this->_vendorFactory->create()->load($vendor_id);
        $vendor_email = $vendor['email'];
        $vendor_name = $vendor['name'];
        $subject = $this->getRequest()->getPost('subject');//"admin_message";
        $message = $this->getRequest()->getPost('msg');
        $sender_email = $admin_email;
        $reciever_email = $vendor_email;
        $sender_name = $vendor_name;
        $date = $this->date->date('Y-m-d');
        $time = $this->date->date('H:i:s');
        $chat_collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', '0')->getLastItem()->getData();

        if (sizeof($chat_collection) == 0) {
            $count = 1;
        } else {
            $count = $chat_collection['vcount'];
            $count++;
        }
        if ($admin_email != "") {
            try {
                $model = $this->_messagingFactory->create();
                $model->setData('vendor_id', $vendor_id);
                $model->setData('subject', $subject);
                $model->setData('message', $message);
                $model->setData('sender_id', '0');
                $model->setData("receiver_name", $sender_name);
                $model->setData("receiver_email", $reciever_email);
                $model->setData("sender_email", $sender_email);
                $model->setData('date', $date);
                $model->setData('time', $time);
                $model->setData('vcount', $count);
                $model->setData('postread', 'new');
                $model->setData('role', 'admin');
                $model->save();

                $data = array();
                $data['receiver_email'] = $vendor_email;
                $data['text'] = $message;
                $data['receiver_name'] = $vendor_name;
                $data['sender_name'] = $sender_name;
                $data['subject'] = $subject;
                $this->_template = 'send_mail_to_vendor';

                if ($sent_mail) {
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
                        ->addTo($vendor_email, $vendor_name);

                    try {
                        $transport = $this->_transportBuilder->getTransport();
                        $transport->sendMessage();
                        $this->inlineTranslation->resume();
                    } catch (\Exception $e) {
                        throw new \Exception (__($e->getMessage()));
                    }

                }
            } catch (\Exception $e) {
                $result['status'] = 'error';
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }
        $result['status'] = 'success';
        $this->messageManager->addSuccessMessage(__('Your Message Has Been Sent To Vendor.'));
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}