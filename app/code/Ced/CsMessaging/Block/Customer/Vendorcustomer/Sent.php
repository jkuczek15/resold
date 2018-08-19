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

namespace Ced\CsMessaging\Block\Customer\Vendorcustomer;

use Ced\CsMessaging\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Backend\Block\Template\Context;
use Ced\CsMessaging\Model\MessagingFactory;

/**
 * Class Sent
 * @package Ced\CsMessaging\Block\Customer\Vendorcustomer
 */
class Sent extends Template
{
    const SEND_TO = 'customer';
    const SORT_BY = 'desc';
    /**
     * @var MessagingFactory
     */
    protected $messagingFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $messagingHelper;

    /**
     * Sent constructor.
     * @param Session $customerSession
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
    )
    {
        $this->_messagingFactory = $messagingFactory;
        $this->customerSession = $customerSession;
        $this->_messagingHelper = $messagingHelper;
        parent::__construct($context, $data);

        $vendor = $this->customerSession->getVendor();
        $vendor_id = $vendor['entity_id'];
        $collection = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('sender_id', $vendor_id)
            ->addFieldToFilter('send_to', self::SEND_TO)
            ->setOrder('chat_id', self::SORT_BY);
        $this->setCollection($collection);

    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
