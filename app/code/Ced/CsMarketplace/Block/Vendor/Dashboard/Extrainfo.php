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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vendor\Dashboard;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

class Extrainfo extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    protected $_associatedOrders = null;
    protected $_associatedPayments = null;


    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $urlModel;

    protected $_session;

    /**
     * Extrainfo constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        UrlFactory $urlFactory
    )
    {
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
        $this->_session = $customerSession;
        $this->urlModel = $urlFactory;
        $this->_objectManager = $objectManager;
        if ($this->getVendorId()) {
            $ordersCollection = $this->getVendor()->getAssociatedOrders()->setOrder('id', 'DESC');
            $main_table = 'main_table';
            $order_total = 'order_total';
            $shop_commission_fee = 'shop_commission_fee';
            $ordersCollection->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr("({$main_table}.{$order_total} - {$main_table}.{$shop_commission_fee})")))->order('created_at DESC')->limit(5);
           
            $this->setVorders($ordersCollection);
        }

    }


    /**
     * Return order view link
     *
     * @param string $order
     * @return String
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('*/vorders/view', ['order_id' => $order->getId()]);
    }

}
