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
 * @package   Ced_CsMarketplace
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ChangeOrderPaymentState implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    protected $_vorders;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsMarketplace\Model\Vorders $vorders,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_objectManager = $objectManager;
        $this->_vorders = $vorders;
        $this->_request = $request;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $helper = $this->_objectManager->get('\Ced\CsMarketplace\Helper\Data');
        $order = $observer->getDataObject()->getOrder();
        $helper->logProcessedData($order->getIncrementId(), \Ced\CsMarketplace\Helper\Data::SALES_ORDER_PAYMENT_STATE_CHANGED);

        $vorders = $this->_vorders->getCollection()
                    ->addFieldToFilter('order_id', ['eq' => $order->getIncrementId()]);
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('ced_csmarketplace_vendor_sales_order');
        $invoiced_item = $this->_request->getPost('invoice');
        if (count($vorders) > 0) {
            foreach ($vorders as $vorder) {
                try {
                    $qtyOrdered = 0;
                    $qtyInvoiced = 0;
                    $invoiced = 0;
                    $vendorId = $vorder->getVendorId();

                    $vorderItems = $this->_objectManager->get('\Magento\Sales\Model\Order\Item')
                                    ->getCollection()->addFieldToSelect('*')
                                    ->addFieldToFilter('vendor_id', $vendorId)
                                    ->addFieldToFilter('order_id', $order->getId());

                    foreach ($vorderItems as $item) {
                        if (isset($invoiced_item)) {
                            foreach ($invoiced_item as $item_id) {
                                if (isset($item_id[$item->getItemId()])) {
                                    $invoiced = $item_id[$item->getItemId()] + (int)$item->getData('qty_invoiced');
                                }
                            }
                        }

                        if ($invoiced == 0) {
                            $invoiced = (int)$item->getData('qty_invoiced');
                        }

                        $qtyOrdered += (int)$item->getQtyOrdered();
                        $qtyInvoiced += (int)$invoiced;
                    }
                    if ($qtyOrdered > $qtyInvoiced) {
                        if ($qtyInvoiced != 0) {
                            $sql = "Update " . $tableName . " Set order_payment_state = " . \Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid::STATE_PARTIALLY_PAID . " where order_id = '{$vorder->getOrderId()}' and vendor_id = '{$vorder->getVendorId()}'";
                        } else {
                            $sql = "Update " . $tableName . " Set order_payment_state = " . \Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid::ORDER_NEW_STATUS . " where order_id = '{$vorder->getOrderId()}' and vendor_id = '{$vorder->getVendorId()}'";
                        }
                    } else {
                        $sql = "Update " . $tableName . " Set order_payment_state = " . \Magento\Sales\Model\Order\Invoice::STATE_PAID . " where order_id = '{$vorder->getOrderId()}' and vendor_id = '{$vorder->getVendorId()}'";
                    }

                    $connection->query($sql);
                    $helper->logProcessedData($vorder->getData(), \Ced\CsMarketplace\Helper\Data::VORDER_PAYMENT_STATE_CHANGED);
                } catch (\Exception $e) {
                    $helper->logException($e);
                    throw new \Magento\Framework\Exception\LocalizedException(__('Error Occured While Placing The Order'));
                }
            }
        }
    }
}  
