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

namespace Ced\CsMessaging\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Ced\CsMessaging\Model\Messaging;

class Save extends Action
{
    protected $resultPageFactory;

    protected $_transportBuilder;
    protected $_messagingFactory;
    protected $inlineTranslation;
    protected $_customerFactory;
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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->_customerFactory = $customerFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->_messagingFactory = $messagingFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->date = $date;
        $this->_storeManager = $storeManager;

    }


    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $customerIds = explode(',',$params['customer_ids']);
        $send_mail = $this->getRequest()->getParam('ismailsent');
        if (!$this->getRequest()->getParam('ismailsent')) {
            $send_mail = 0;
        }
        $admin_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
        $sender_email = $admin_email;
        $subject = $this->getRequest()->getPost('subject');
        $message = $this->getRequest()->getPost('message');
        $date = $this->date->date('Y-m-d');
        $time = $this->date->date('H:i:s');
        if (count($customerIds))
        {
            foreach ($customerIds as $cId)
            {
                $customer = $this->_customerFactory->create()->load($cId);
                $customer_email = $customer->getEmail();
                $customer_name = $customer->getName();
                $reciever_email = $customer_email;
                $reciever_name = $customer_name;
            
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
                        $model->setData('vendor_id', 0);
                        $model->setData('subject', $subject);
                        $model->setData('message', $message);
                        $model->setData('sender_id', '0');
                        $model->setData("receiver_name", $reciever_name);
                        $model->setData("receiver_email", $reciever_email);
                        $model->setData("sender_email", $admin_email);
                        $model->setData('date', $date);
                        $model->setData('time', $time);
                        $model->setData('vcount', $count);
                        $model->setData('postread', 'new');
                        $model->setData('role', 'admin');
                        $model->setData('send_mail', $send_mail);
                        $model->setData('is_mail_sent', '0');
                        $model->setData('send_to', 'customer');
                        $model->save();


                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage(__($e->getMessage()));
                        $this->_redirect('customer/index/index');
                    }
                }
            } 
        }
        $this->messageManager->addSuccessMessage(__('Your message has been sent to customers.'));
        $this->_redirect('customer/index/index');
    }
}