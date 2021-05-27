<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMessaging
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMessaging\Block\Adminhtml\Message\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\ObjectManagerInterface;
use Ced\CsMessaging\Model\MessagingFactory;
/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{


    protected function _construct()
    {
        parent::_construct();
        $this->setId('msg_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Compose Message'));

    }

    /**
     * Tabs constructor.
     * @param Context $context
     * @param EncoderInterface $jsonEncoder
     * @param Session $authSession
     * @param ObjectManagerInterface $objectManager
     * @param MessagingFactory $messagingFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        ObjectManagerInterface $objectManager,
        MessagingFactory $messagingFactory,
        array $data = []
    )
    {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->_objectManager = $objectManager;
        $this->_messagingFactory = $messagingFactory;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $admin_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
        $vendorChatData=$this->_messagingFactory->create()->getCollection()
                                                ->addFieldToFilter('postread','new')
                                                ->addFieldToFilter('receiver_email',$admin_email)
                                                ->addFieldToFilter('role','vendor');
        $CustomerchatData=$this->_messagingFactory->create()->getCollection()
                                                ->addFieldToFilter('postread','new')
                                                ->addFieldToFilter('receiver_email',$admin_email)
                                                ->addFieldToFilter('role','customer');


        $this->addTab('General', array(
            'label'     => __('Compose'),
            'title'     => __('Compose'),
            'content'   => $this->getLayout()->createBlock('Ced\CsMessaging\Block\Adminhtml\Message\Compose\Edit\Tab\Main')->toHtml(),
        ));
        $vmessage = '';
        $cmessage = '';
        if (count($vendorChatData))
        {
            $vmessage = '('.count($vendorChatData).')';
        }

        if (count($CustomerchatData))
        {
            $cmessage = '('.count($CustomerchatData).')';
        }

        $this->addTab('Vendor_Inbox', array(
            'label'     => __('Seller Inbox '.$vmessage),
            'title'     => __('Seller Inbox'),
            'content'   => $this->getLayout()->createBlock('Ced\CsMessaging\Block\Adminhtml\Message\Inbox\Edit\Tab\Main')->toHtml(),
        ));
        $this->addTab('Customer_Inbox', array(
            'label'     => __('Customer Inbox '.$cmessage),
            'title'     => __('Customer Inbox'),
            'content'   => $this->getLayout()->createBlock('Ced\CsMessaging\Block\Adminhtml\Message\CustomerInbox\Edit\Tab\Main')->toHtml(),
        ));
        $this->addTab('Sendbox', array(
            'label'     => __('Sent'),
            'title'     => __('Sent'),
            'content'   => $this->getLayout()->createBlock('Ced\CsMessaging\Block\Adminhtml\Message\Send\Edit\Tab\Main')->toHtml(),
        ));

        return parent::_prepareLayout();
    }

}
