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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Helper;

class Report extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_vendor = null;
    protected $_objectManager = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }
    
    
    public function getTotalOrdersByCountry($vendor) 
    {
        $result = [];
        if ($vendor && $vendor->getId()) {
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\VordersFactory')->create();
            $orders = $model->getCollection()->addFieldToFilter('vendor_id', $vendor->getId());
            foreach($orders as $order) {                
                $countryId = strtolower($order->getShippingCountryCode());
                if(!strlen($countryId)) {
                    $mainOrder = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($order->getOrderId());
                    if ($mainOrder && $mainOrder->getId()) {
                        $countryId = strtolower($mainOrder->getBillingAddress()->getData('country_id'));    
                    }
                }
                
                if(strlen($countryId)) {
                    if (isset($result[$countryId]['total'])) {
                        $result[$countryId]['total'] += 1; 
                    }
                    else { 
                        $result[$countryId]['total'] = 1; 
                    }
                    if (isset($result[$countryId]['amount'])) {
                        $result[$countryId]['amount'] += (double) $order->getOrderTotal(); 
                    }
                    else { 
                        $result[$countryId]['amount'] = (double) $order->getOrderTotal(); 
                    }
                }
            }
        }
        return $result;
    }
    
    public function getChartData($vendor, $type='order', $range = 'day') 
    {
        $results = [];
        if ($vendor && $vendor->getId()) {
            $this->_vendor = $vendor;
            switch ($range) {
            default:
            case 'day':
                for ($i = 0; $i < 24; $i++) {
                    $results[$i] = array(
                    'hour'  => $i,
                    'total' => 0
                    );
                } 
                $model = $this->_getReportModel($type, $range); 
                foreach ($model as $result) {
                    $results[$result['hour']] = array(
                    'hour'  => $result['hour'],
                    'total' => $result['total']
                    );
                }
                break;
                    
            case 'week':
                $date_start = strtotime('-' . date('w') . ' days');

                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', $date_start + ($i * 86400));

                    $results[date('w', strtotime($date))] = array(
                    'day'   => date('D', strtotime($date)),
                    'total' => 0
                    );
                }
                $model = $this->_getReportModel($type, $range);
                foreach ($model as $result) {
                    $results[date('w', strtotime($result['created_at']))] = array(
                    'day'   => date('D', strtotime($result['created_at'])),
                    'total' => $result['total']
                    );
                }
                break;
                    
            case 'month':
                for ($i = 1; $i <= date('t'); $i++) {
                    $date = date('Y') . '-' . date('m') . '-' . $i;

                    $results[date('j', strtotime($date))] = array(
                    'day'   => date('d', strtotime($date)),
                    'total' => 0
                    );
                }
              
                $model = $this->_getReportModel($type, $range);
                foreach ($model as $result) {
                    $results[date('j', strtotime($result['created_at']))] = array(
                    'day'   => date('d', strtotime($result['created_at'])),
                    'total' => $result['total']
                    );
                }
                break;
            case 'year':
                for ($i = 1; $i <= 12; $i++) {
                    $results[$i] = array(
                    'month' => date('M', mktime(0, 0, 0, $i)),
                    'total' => 0
                    );
                }
                $model = $this->_getReportModel($type, $range);
                foreach ($model as $result) {
                    $results[date('n', strtotime($result['created_at']))] = array(
                    'month' => date('M', strtotime($result['created_at'])),
                    'total' => $result['total']
                    );
                }
                break;
            }
        }
        return $results;
    }
    
    protected function _getReportModel($model = 'order', $range = 'day') 
    {
        if ($this->_vendor != null && $this->_vendor && $this->_vendor->getId()) {
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\VordersFactory')->create();
            $model = $model->getCollection()->addFieldToFilter('vendor_id',$this->_vendor->getId());
            switch($model) {
            default:
            case 'order' :  switch($range) {
                default:
                case 'day'  : 
                    $model->getSelect()
                        ->reset('columns')
                        ->columns("COUNT(*) AS total")
                        ->columns("HOUR(created_at) AS hour")
                        ->where("DATE(created_at) = DATE(NOW())")
                        ->group("HOUR(created_at)")
                        ->order("created_at ASC");
                    break;
                case 'week' :
                    $date_start = strtotime('-' . date('w') . ' days');
                    $model->getSelect()
                        ->reset('columns')
                        ->columns("created_at")
                        ->columns("COUNT(*) AS total")
                                                        
                        ->where("DATE(created_at) >= DATE('" . date('Y-m-d', $date_start) . "')")
                        ->group("DAYNAME(created_at)");
                    break;                                    
                case 'month': 
                    $model->getSelect()
                        ->reset('columns')
                        ->columns("created_at")
                        ->columns("COUNT(*) AS total")
                        ->where("DATE(created_at) >= '" . date('Y') . '-' . date('m') . '-1' . "'")
                        ->group("DATE(created_at)");
                    break;                                    
                case 'year' : 
                    $model->getSelect()
                        ->reset('columns')
                        ->columns("created_at")
                        ->columns("COUNT(*) AS total")
                        ->where("YEAR(created_at) = YEAR(NOW())")
                        ->group("MONTH(created_at)");
                    break;                                
            } 
                break;        
            case 'qty'   : //$model = $this->_vendor->getAssociatedOrders(); 
                break;
            case 'sale'  : //$model = $this->_vendor->getAssociatedOrders(); 
                break;
            }
            return $model && count($model)?$model->getData():array();
        }
        return false;
    }
    
    public function getVordersReportModel($vendor,$range = 'day',$from_date,$to_date,$status=\Ced\CsMarketplace\Model\Vorders::STATE_PAID) 
    {
        $this->_vendor=$vendor;
        if ($this->_vendor != null && $this->_vendor && $this->_vendor->getId()) {
            $from_date=date("Y-m-d 00:00:00", strtotime($from_date));
            $to_date=date("Y-m-d 23:59:59", strtotime($to_date));
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\VordersFactory')->create();
            $model = $model->getCollection()->addFieldToFilter('vendor_id',$this->_vendor->getId());
            switch($range) {
            default:$model = $this->_vendor->getAssociatedOrders(); 
                break;
            case 'day'  :
                $model->getSelect()
                    ->reset('columns')
                    ->columns("DATE(created_at) AS period")
                    ->columns("COUNT(*) AS order_count")
                    ->columns("SUM(product_qty) AS product_qty")
                    ->columns("SUM(order_total) as order_total")
                    ->columns("SUM(shop_commission_fee) AS commission_fee")
                    ->columns("(SUM(order_total) - SUM(shop_commission_fee)) AS net_earned")
                    ->where("created_at>='".$from_date."'")
                    ->where("created_at<='".$to_date."'")
                    ->group("DATE(created_at)")
                    ->order("created_at ASC");
                break;
            case 'month':
                $model->getSelect()
                    ->reset('columns')
                    ->columns("CONCAT(MONTH(created_at),CONCAT('-',YEAR(created_at))) AS period")
                    ->columns("COUNT(*) AS order_count")
                    ->columns("SUM(product_qty) AS product_qty")
                    ->columns("SUM(order_total) AS order_total")
                    ->columns("SUM(shop_commission_fee) AS commission_fee")
                    ->columns("(SUM(order_total) - SUM(shop_commission_fee)) AS net_earned")
                    ->where("created_at>='".$from_date."'")
                    ->where("created_at<='".$to_date."'")
                    ->group("YEAR(created_at)")
                    ->group("MONTH(created_at)");
                break;
            case 'year' :
                $model->getSelect()
                    ->reset('columns')
                    ->columns("YEAR(created_at) AS period")
                    ->columns("COUNT(*) AS order_count")
                    ->columns("SUM(order_total) AS order_total")
                    ->columns("SUM(product_qty) AS product_qty")
                    ->columns("SUM(shop_commission_fee) AS commission_fee")
                    ->columns("(SUM(order_total) - SUM(shop_commission_fee)) AS net_earned")
                    ->where("created_at>='".$from_date."'")
                    ->where("created_at<='".$to_date."'")
                    ->group("YEAR(created_at)");
                break;
                
            }
            if($status!="*") {
                if($status==\Ced\CsMarketplace\Model\Vorders::STATE_OPEN) {
                    $order_status=\Ced\CsMarketplace\Model\Vorders::STATE_PAID; 
                }
                if($status== \Ced\CsMarketplace\Model\Vorders::STATE_PAID) {
                    $order_status=\Ced\CsMarketplace\Model\Vorders::STATE_PAID; 
                }
                if($status==\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED) {
                    $order_status=\Ced\CsMarketplace\Model\Vorders::STATE_CANCELED; 
                }
                $model->getSelect()
                    ->where("payment_state='".$status."'")
                    ->where("order_payment_state='".$order_status."'");
            }
            return $model && count($model)?$model:array();
        }
        return false;
    }
    
    public function getVproductsReportModel($vendorId,$from_date = '',$to_date = '' , $group = true) 
    {
        $ordersCollection = $this->_objectManager->get('Magento\Reports\Model\ResourceModel\Product\Sold\Collection');
        $from = $to = '';
        if ($from_date != '' && $to_date != '') {
            $from=date("Y-m-d 00:00:00", strtotime($from_date));
            $to=date("Y-m-d 23:59:59", strtotime($to_date));
        }
        $compositeTypeIds = $this->_objectManager->get('Magento\Catalog\Model\Product\Type')->getCompositeTypes();
        $product = $this->_objectManager->get('Magento\Catalog\Model\Product');
        $coreResource = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $adapter = $coreResource->getConnection('read');
        $orderTableAliasName = $adapter->quoteIdentifier('order');
    
        $orderJoinCondition   = [
                $orderTableAliasName . '.entity_id = order_items.order_id',
                $adapter->quoteInto("{$orderTableAliasName}.state <> ?", \Magento\Sales\Model\Order::STATE_CANCELED),
            ];
    
        $productJoinCondition = [
                $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
                'e.entity_id = order_items.product_id'
            ];
    
        if (($from != '' && $to != '') || $group) {
            $fieldName = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }
        
        $ordersCollection->getSelect()->reset()
            ->from(
                array('order_items' =>$coreResource->getTableName('sales_order_item')),
                array(
                'ordered_qty' => 'SUM(order_items.qty_ordered)',
                'order_item_name' => 'order_items.name',
                'order_item_total_sales' => 'SUM(order_items.row_total)',
                'sku'=>'order_items.sku'
                )
            )
            ->joinInner(
                array('order' => $coreResource->getTableName('sales_order')),
                implode(' AND ', $orderJoinCondition),
                array()
            )
            ->joinLeft(
                array('e' => $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product')->getEntityTable()),
                implode(' AND ', $productJoinCondition),
                array(
                            'entity_id' => 'order_items.product_id',
                            'type_id' => 'e.type_id',
                    )
            )
            ->where('parent_item_id IS NULL')
            ->where('vendor_id="'.$vendorId.'"');
        if($group) { $ordersCollection->getSelect()->group('order_items.product_id'); 
        }
        $ordersCollection->getSelect()->having('SUM(order_items.qty_ordered) > ?', 0);
        return $ordersCollection;
    }

    /**
     * Prepare between sql
     *
     * @param  string $fieldName Field name with table suffix ('created_at' or 'main_table.created_at')
     * @param  string $from
     * @param  string $to
     * @return string Formatted sql string
     */
    protected function _prepareBetweenSql($fieldName, $from, $to)
    {
        $coreResource   = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $adapter              = $coreResource->getConnection('read');
        return sprintf(
            '(%s >= %s AND %s <= %s)',
            $fieldName,
            $adapter->quote($from),
            $fieldName,
            $adapter->quote($to)
        );
    }
}
