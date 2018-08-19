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
 * @category  Ced;
 * @package   Ced_CsMessaging
 * @author    CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMessaging\Block;

use Ced\CsMessaging\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Block\Template\Context;
use Ced\CsMessaging\Model\MessagingFactory;

class Adminread extends Template
{
    /**
     * Adminread constructor.
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     * @param MessagingFactory $messagingFactory
     * @param Data $messagingHelper
     * @param array $data
     */
    public function __construct(
        Session $customerSession,
        Context $context,
        MessagingFactory $messagingFactory,
        Data $messagingHelper,
        array $data = []
    ) {

        $this->_messagingFactory = $messagingFactory;
        $this->customerSession = $customerSession;
        $this->_messagingHelper = $messagingHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getinboxcollection()
    {
        $receiver_name=$this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $vendor=$this->customerSession->getVendor();
        $vendor_name=$vendor['name'];
        $collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', '0')->addFieldToFilter('receiver_name', $vendor_name)->addFieldToFilter('postread', 'new')->setOrder('chat_id', 'desc')->getData();
        return $collection;

    }

}
