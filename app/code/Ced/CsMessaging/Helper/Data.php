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

namespace Ced\CsMessaging\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Mail\Template\TransportBuilder;
use Ced\CsMessaging\Model\MessagingFactory;

class Data extends AbstractHelper
{

    const XML_PATH_CUSTOMER_EMAIL_TO_VENDOR = 'csmessaging/vendor/customeremail_to_vendor';
    const XML_PATH_VENDOR_EMAIL_TO_ADMIN = 'csmessaging/vendor/email_to_admin';
    const XML_PATH_ADMIN_EMAIL_TO_VENDOR = 'csmessaging/vendor/email_to_vendor';
    const XML_PATH_VENDOR_EMAIL_TO_CUSTOMER = 'csmessaging/vendor/vendoremail_to_customer';
    const ADMIN_STORE_EMAIL_PATH = 'trans_email/ident_general/email';
    const ADMIN_STORE_NAME_PATH = 'trans_email/ident_general/name';
    const MESSAGE_STATUS_NEW = 'new';
    const SEND_TO_VENDOR = 'vendor';
    const CAN_ADMIN_SEND_MAIL = 'ced_csmarketplace/csmessaging/admin_send_mail';

    /**
     * @var \Magento\Framework\ObjectManagerInterface|null
     */
    protected $_objectManager = null;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param Session $customerSession
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(Context $context,
                                ObjectManagerInterface $objectManager,
                                Session $customerSession,
                                TransportBuilder $transportBuilder,
                                MessagingFactory $messagingFactory

    )
    {

        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_customerSession = $customerSession;
        $this->_messagingFactory = $messagingFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getAdminStoreEmail()
    {
        return $this->_scopeConfig->getValue(self::ADMIN_STORE_EMAIL_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAdminStoreName()
    {
        return $this->_scopeConfig->getValue(self::ADMIN_STORE_NAME_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getVendorInboxCountForAdmin()
    {
        $vendor = $this->_customerSession->getVendor();
        $vendorEmail = $vendor['email'];
        $vendorInboxData = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('sender_id', '0')
            ->addFieldToFilter('receiver_email', $vendorEmail)
            ->addFieldToFilter('postread', self::MESSAGE_STATUS_NEW)
            ->addFieldToFilter('send_to', self::SEND_TO_VENDOR)->getData();
        return count($vendorInboxData);
    }

    /**
     * @return int
     */
    public function getVendorInboxCountForCustomer()
    {
        $vendor = $this->_customerSession->getVendor();
        $vendorEmail = $vendor['email'];
        $vendorId = $vendor['entity_id'];

        $vendorInboxData = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('receiver_email', $vendorEmail)
            ->addFieldToFilter('postread', self::MESSAGE_STATUS_NEW)
            ->addFieldToFilter('sender_id', ['neq'=>$vendorId])
            ->addFieldToFilter('sender_id', ['neq'=>0])
            ->addFieldToFilter('role', 'customer')
            ->getData();
        return count($vendorInboxData);
    }

    /**
     * @return int
     */
    public function getCustomerInboxCountForAdmin()
    {
        $customerEmail = $this->_customerSession->getCustomer()->getEmail();
        $vendorInboxData = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('receiver_email', $customerEmail)
            ->addFieldToFilter('postread', self::MESSAGE_STATUS_NEW)
            ->addFieldToFilter('sender_id', 0)
            ->addFieldToFilter('role', 'admin')
            ->addFieldToFilter('send_to', 'customer')
            ->getData();
        return count($vendorInboxData);
    }

    /**
     * @return int
     */
    public function getCustomerInboxCountForVendor()
    {
        $customer = $this->_customerSession->getCustomer();
        $customerEmail = $customer->getEmail();
        $customerId = $customer->getId();
        $vendorInboxData = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('receiver_email', $customerEmail)
            ->addFieldToFilter('postread', self::MESSAGE_STATUS_NEW)
            ->addFieldToFilter('sender_id', ['neq'=>$customerId])
            ->addFieldToFilter('sender_id', ['neq'=>0])
            ->addFieldToFilter('role', 'vendor')
            ->getData();
        return count($vendorInboxData);
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getScopeConfigValue($path)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}

