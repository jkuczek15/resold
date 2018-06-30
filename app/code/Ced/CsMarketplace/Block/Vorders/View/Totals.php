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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vorders\View;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Associated array of totals
     * array(
     *  $totalCode => $totalObject
     * )
     *
     * @var array
     */
     
    protected $_totals;
    protected $_order = null;
    protected $_vorder = null;
    protected $_registry;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->_objectManager = $objectManager; 
        $this->_registry = $registry;
    }

    /**
     * Initialize self totals and children blocks totals before html building
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->_initTotals();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if (method_exists($child, 'initTotals')) {
                $child->initTotals();
            }
        }
        return parent::_beforeToHtml();
    }
    
    /**
     * Get order object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order === null) {
            if ($this->hasData('order')) {
                $this->_order = $this->_getData('order');
            } elseif ($this->_registry->registry('current_order')) {
                $this->_order = $this->_registry->registry('current_order');
            } elseif ($this->getParentBlock()->getOrder()) {
                $this->_order = $this->getParentBlock()->getOrder();
            }
        }
        return $this->_order;
    }
    
    /**
     * Get order object
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVOrder()
    {
        if ($this->_vorder === null) {
            if ($this->hasData('vorder')) {
                $this->_vorder = $this->_getData('vorder');
            } elseif ($this->_registry->registry('current_vorder')) {
                $this->_vorder = $this->_registry->registry('current_vorder');
            } elseif ($this->getParentBlock()->getOrder()) {
                $orderId = (int) $this->getRequest()->getParam('order_id');
                $this->_vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($orderId);
            }
        }
        return $this->_vorder;
    }
    
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }
    
    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getOrder();
    }
    
    /**
     * Get totals vsource object
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVSource()
    {
        return $this->getVOrder();
    }
    
    /**
     * Format total value based on order currency
     *
     * @param   Varien_Object $total
     * @return  string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->displayPrices(
                    $this->getOrder(),
                    $total->getBaseValue(),
                    $total->getValue()
            );
        }
        return $total->getValue();
    }
    
    /**
     * Get "double" prices html (block with base and place currency)
     *
     * @param   Varien_Object $dataObject
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        $cedOrder = false;
        if ($dataObject instanceof \Magento\Sales\Model\Order) {
            $cedOrder = $dataObject;
        } else {
            $cedOrder = $dataObject->getOrder();
        }
    
        if ($cedOrder && $cedOrder->isCurrencyDifferent()) {
            $res= $cedOrder->formatBasePrice($basePrice);
            $res.= $separator;
            $res.= '['.$cedOrder->formatPrice($price).']';
        } elseif ($cedOrder) {
            $res = $cedOrder->formatPrice($price);
            if ($strong) {
                $res = $res;
            }
        } else {
            $res = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->formatPrice($price);
            if ($strong) {
                $res = $res;
            }
        }
        return $res;
    }

    /**
     * Initialize order totals array
     * @return $this
     */
    protected function _initTotals()
    {
        $vsource = $this->getVSource();
        $source = $this->getSource();
        $this->_totals = [];
        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(array(
                'code'      => 'subtotal',
                'value'     => $vsource->getPurchaseSubtotal(),
                'base_value'=> $vsource->getBaseSubtotal(),
                'label'     => __('Subtotal')
        ));
    
        /**
         * Add discount
         */
        if (((float)$vsource->getBaseDiscountAmount()) != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = __('Discount (%1)', $this->getSource()->getDiscountDescription());
            } else {
                $discountLabel = __('Discount');
            }
            $this->_totals['discount'] = new \Magento\Framework\DataObject(array(
                    'code'      => 'discount',
                    'value'     => $vsource->getPurchaseDiscountAmount(),
                    'base_value'=> $vsource->getBaseDiscountAmount(),
                    'label'     => $discountLabel
            ));
        }
        
        $this->_totals['shop_commission_fee'] = new \Magento\Framework\DataObject(array(
        		'code'      => 'shop_commission_fee',
        		'strong'    => true,
        		'value'     =>$vsource->getShopCommissionFee(),
        		'base_value'=> $vsource->getShopCommissionBaseFee(),
        		'label'     => __('Commission Fee'),
        		'area'      => 'footer'
        ));
        
        $this->_totals['grand_total'] = new \Magento\Framework\DataObject(array(
                'code'      => 'grand_total',
                'strong'    => true,
                'value'     =>$vsource->getPurchaseGrandTotal(),
                'base_value'=> $vsource->getBaseGrandTotal(),
                'label'     => __('Grand Total'),
                'area'      => 'footer'
        ));
        $orderEarn = $vsource->getOrderTotal()- $vsource->getShopCommissionFee();
        $baseOrderEarn = $vsource->getBaseOrderTotal()- $vsource->getShopCommissionBaseFee();
        $this->_totals['vendor_earn'] = new \Magento\Framework\DataObject(array(
        		'code'      => 'vendor_earn',
        		'strong'    => true,
        		'value'     =>$orderEarn,
        		'base_value'=>$baseOrderEarn,
        		'label'     => __('Net Vendor Earn'),
        		'area'      => 'footer'
        ));
        return $this;
    }
    

    /**
     * Add new total to totals array after specific total or before last total by default
     * @param Varien_Object $total
     * @param null $after
     * @return $this
     */
    public function addTotal(\Magento\Framework\DataObject $total, $after=null)
    {
        if ($after !== null && $after != 'last' && $after != 'first') {
            $csTotals = [];
            $added = false;
            foreach ($this->_totals as $code => $item) {
                $csTotals[$code] = $item;
                if ($code == $after) {
                    $added = true;
                    $csTotals[$total->getCode()] = $total;
                }
            }
            if (!$added) {
                $last = array_pop($csTotals);
                $csTotals[$total->getCode()] = $total;
                $csTotals[$last->getCode()] = $last;
            }
            $this->_totals = $csTotals;
        } elseif ($after=='last')  {
            $this->_totals[$total->getCode()] = $total;
        } elseif ($after=='first')  {
            $csTotals = array($total->getCode()=>$total);
            $this->_totals = array_merge($csTotals, $this->_totals);
        } else {
            $last = array_pop($this->_totals);
            $this->_totals[$total->getCode()] = $total;
            $this->_totals[$last->getCode()] = $last;
        }
        return $this;
    }
    


    /**
     * Add new total to totals array before specific total or after first total by default
     * @param Varien_Object $total
     * @param null $before
     * @return $this
     */
    public function addTotalBefore(\Magento\Framework\DataObject $total, $before=null)
    {
        if ($before !== null) {
            if (!is_array($before)) {
                $before = array($before);
            }
            foreach ($before as $beforeTotals) {
                if (isset($this->_totals[$beforeTotals])) {
                    $csTotals = [];
                    foreach ($this->_totals as $code => $item) {
                        if ($code == $beforeTotals) {
                            $csTotals[$total->getCode()] = $total;
                        }
                        $csTotals[$code] = $item;
                    }
                    $this->_totals = $csTotals;
                    return $this;
                }
            }
        }
        $csTotals = [];
        $first = array_shift($this->_totals);
        $csTotals[$first->getCode()] = $first;
        $csTotals[$total->getCode()] = $total;
        foreach ($this->_totals as $code => $item) {
            $csTotals[$code] = $item;
        }
        $this->_totals = $csTotals;
        return $this;
    }

    /**
     * Delete total by specific
     * @param $codeName
     * @return $this
     */
    public function removeTotal($codeName)
    {
        unset($this->_totals[$codeName]);
        return $this;
    }

    /**
     * Get Total object by codeName
     * @param $codeName
     * @return bool|mixed
     */
    public function getTotal($codeName)
    {
        if (isset($this->_totals[$codeName])) {
            return $this->_totals[$codeName];
        }
        return false;
    }


    /**
     * get totals array for visualization
     * @param null $csarea
     * @return array
     */
    public function getTotals($csarea=null)
    {
        $totals = array();
        if ($csarea === null) {
            $totals = $this->_totals;
        } else {
            $csarea = (string)$csarea;
            foreach ($this->_totals as $total) {
                $totalArea = (string) $total->getArea();
                if ($totalArea == $csarea) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }
    

    /**
     * Apply sort orders to totals array.
     * @param $order
     * @return $this
     */
    public function applySortOrder($order)
    {
        return $this;
    }
}