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

namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab;

class Vpayments extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Ced\CsMarketplace\Model\VPaymentsFactory
     */
    protected $_vpaymentFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $_objectManager;

    /**
     * @var \Ced\CsMarketplace\Model\Status
     */
    // protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMarketplace\Model\VPaymentsFactory $vpaymentFactory
     * @param \Ced\CsMarketplace\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        array $data = []
    )
    {

        $this->_vpaymentFactory = $vpaymentFactory;
        //$this->_status = $status;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->moduleManager = $moduleManager;
        $this->_objectManager = $objectmanager;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
        $this->setVendor($this->_coreRegistry->registry('vendor_data'));
        $this->setPendingAmount(0.00);
        $this->setPendingTransfers(0);
        $this->setPaidAmount(0.00);
        $this->setCanceledAmount(0.00);
        $this->setRefundableAmount(0.00);
        $this->setRefundedAmount(0.00);
        $this->setEarningAmount(0.00);

        if ($this->getVendor() && $this->getVendor()->getId()) {

            $paymentHelper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Payment');

            $collection = $paymentHelper->_getTransactionsStats($this->getVendor());

            if (count($collection) > 0) {

                foreach ($collection as $stats) {
                    switch ($stats->getPaymentState()) {
                        case \Ced\CsMarketplace\Model\Vorders::STATE_OPEN :
                            $this->setPendingAmount($stats->getNetAmount());
                            $this->setPendingTransfers($stats->getCount() ? $stats->getCount() : 0);
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_PAID :
                            $this->setPaidAmount($stats->getNetAmount());
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_CANCELED :
                            $this->setCanceledAmount($stats->getNetAmount());
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_REFUND :
                            $this->setRefundableAmount($stats->getNetAmount());
                            break;
                        case \Ced\CsMarketplace\Model\Vorders::STATE_REFUNDED :
                            $this->setRefundedAmount($stats->getNetAmount());
                            break;
                    }
                }
            }
            $this->setEarningAmount($this->getVendor()->getAssociatedPayments()->getFirstItem()->getBalance());
        }

        $this->setTemplate('vendor/entity/edit/tab/vpayments.phtml');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $grid = $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid', 'vpayments.grid');
        $this->setChild('vpayments.grid', $grid);
        return $this;
    }

    public function getObjectManager()
    {
        return $this->_objectManager;
    }


}