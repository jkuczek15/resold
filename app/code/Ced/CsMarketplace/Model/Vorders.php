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
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model;
use Magento\Framework\Api\AttributeValueFactory;
class Vorders extends \Ced\CsMarketplace\Model\FlatAbstractModel
{
    /**
     * Payment states
     */
    const STATE_OPEN       = 1;
    const STATE_PAID       = 2;
    const STATE_CANCELED   = 3;
    const STATE_REFUND     = 4;
    const STATE_REFUNDED   = 5;
    
    const ORDER_NEW_STATUS=1;
    const ORDER_CANCEL_STATUS=3;
    
    protected $_items = null;
    
    protected static $_states;
    
    protected $_eventPrefix      = 'csmarketplace_vorders';
    protected $_eventObject      = 'vorder';
    public $_vendorstatus=null;
    protected $customer;
    protected $_dataHelper;
    protected $_aclHelper;
    protected $_objectManager;
    
    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory       $extensionFactory
     * @param AttributeValueFactory                                   $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {


        $this->_objectManager=$objectInterface;
        $this->_coreRegistry= $registry;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        

           
    }
    
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        
        $this->_init('Ced\CsMarketplace\Model\ResourceModel\Vorders');
    }
    
    /**
     * Retrieve vendor order states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
            self::STATE_OPEN       => __('Pending'),
            self::STATE_PAID       => __('Paid'),
            self::STATE_CANCELED   => __('Canceled'),
            self::STATE_REFUND     => __('Refund'),
            self::STATE_REFUNDED   => __('Refunded'),
            );
        }
        return self::$_states;
    }
    
    /**
     * Check vendor order pay action availability
     *
     * @return bool
     */
    public function canPay()
    {
        return $this->getOrderPaymentState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID 
        && 
        $this->getPaymentState() == self::STATE_OPEN;
    }
    
    /**
     * Check vendor order cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getPaymentState() == self::STATE_OPEN;
    }
    
    /**
     * Check vendor order refund action availability
     *
     * @return bool
     */
    public function canMakeRefund()
    {
        return $this->getOrderPaymentState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID 
        && 
        $this->getPaymentState() == self::STATE_PAID;
    }
    
    /**
     * Check vendor order refund action availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return $this->getOrderPaymentState() == \Magento\Sales\Model\Order\Invoice::STATE_PAID 
        && 
        $this->getPaymentState() == self::STATE_REFUND;
    }
    
    /**
     * Get Ordered Items associated to customer
     * params: $order Object, $vendorId int
     * return order_item_collection
     */
    public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        
        $incrementId = $this->getOrderId();
        $vendorId = $this->getVendorId();
        
        $order  = $this->getOrder();
        
        if (is_null($this->_items)) {
            $this->_items = 

            $this->_objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection')
                ->setOrderFilter($order)
                ->addFieldToFilter('vendor_id', $vendorId);
            
            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }
            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    if($item->getVendorId() == $vendorId) {
                        $item->setOrder($order); 
                    }
                }
            }
        }
        
        return $this->_items;
    }
    
    /**
     * Get Ordered Items associated to customer
     * params: $order Object, $vendorId int
     * return order_item_collection
     */
    public function getOrder($incrementId = false)
    {
        if(!$incrementId) { $incrementId = $this->getOrderId(); 
        }
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
        return $order;
        
    }
    
    /**
     * Get Vordered Subtotal
     * return float
     */
    public function getPurchaseSubtotal()
    {
        $items = $this->getItemsCollection();
        $subtotal  = 0;
        foreach($items as $_item){
            $subtotal +=$_item->getRowTotal();
        }
        return $subtotal;
    }
    
    /**
     * Get Vordered base Subtotal
     * return float
     */
    public function getBaseSubtotal()
    {
        $items = $this->getItemsCollection();
        $basesubtotal  = 0;
        foreach($items as $_item){
            $basesubtotal +=$_item->getBaseRowTotal();
        }
        return $basesubtotal;
    }
    
    
    /**
     * Get Vordered Grandtotal
     * return float
     */
    public function getPurchaseGrandTotal()
    {
        $items = $this->getItemsCollection();
        $grandtotal  = 0;
        foreach($items as $_item){
            $grandtotal +=$_item->getRowTotal()+ $_item->getTaxAmount()+ $_item->getHiddenTaxAmount()+ $_item->getWeeeTaxAppliedRowAmount()- $_item->getDiscountAmount();
        }
        return $grandtotal;
    }
    
    /**
     * Get Vordered base Grandtotal
     * return float
     */
    public function getBaseGrandTotal()
    {
        $items = $this->getItemsCollection();
        $basegrandtotal  = 0;
        foreach($items as $_item){
            $basegrandtotal +=$_item->getBaseRowTotal()+ $_item->getBaseTaxAmount() + $_item->getBaseHiddenTaxAmount() + $_item->getBaseWeeeTaxAppliedRowAmount() - $_item->getBaseDiscountAmount();
        }
        return $basegrandtotal;
    }
    
    
    
    /**
     * Get Vordered tax
     * return float
     */
    public function getPurchaseTaxAmount()
    {
        $items = $this->getItemsCollection();
        $tax  = 0;
        foreach($items as $_item){
            $tax +=$_item->getTaxAmount()+ $_item->getHiddenTaxAmount()+ $_item->getWeeeTaxAppliedRowAmount();
        }
        return $tax;
    }
    
    /**
     * Get Vordered tax
     * return float
     */
    public function getBaseTaxAmount()
    {
        $items = $this->getItemsCollection();
        $tax  = 0;
        foreach($items as $_item){
            $tax +=$_item->getBaseTaxAmount()+ $_item->getBaseHiddenTaxAmount()+ $_item->getBaseWeeeTaxAppliedRowAmount();
        }
        return $tax;
    }
    
    /**
     * Get Vordered Discount
     * return float
     */
    public function getPurchaseDiscountAmount()
    {
        $items = $this->getItemsCollection();
        $discount  = 0;
        foreach($items as $_item){
            $discount +=$_item->getDiscountAmount();
        }
        return $discount;
    }
    
    /**
     * Get Vordered Discount
     * return float
     */
    public function getBaseDiscountAmount()
    {
        $items = $this->getItemsCollection();
        $discount  = 0;
        foreach($items as $_item){
            $discount +=$_item->getBaseDiscountAmount();
        }
        return $discount;
    }
    
    /**
     * Calculate the commission fee
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function collectCommission() 
    {
        if ($this->getData('vendor_id') && $this->getData('base_to_global_rate') && $this->getData('order_total')) {
            $order = $this->getCurrentOrder();

            $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Acl')->setStoreId($order->getStoreId())->setOrder($order)->setVendorId($this->getData('vendor_id'));
            $commissionSetting = $helper->getCommissionSettings($this->getData('vendor_id'));
            $commissionSetting['item_commission'] = $this->getData('item_commission');

            $commission = $helper->calculateCommission($this->getData('order_total'), $this->getData('base_order_total'), $this->getData('base_to_global_rate'), $commissionSetting);

            $this->setShopCommissionTypeId($commissionSetting['type']);
            $this->setShopCommissionRate($commissionSetting['rate']);
            $this->setShopCommissionBaseFee($commission['base_fee']);
            $this->setShopCommissionFee($commission['fee']);
            $this->setCreatedAt($this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate());
            $this->setPaymentState(self::STATE_OPEN);
            if(isset($commission['item_commission'])) {
                $this->setItemsCommission($commission['item_commission']);
            }
            $this->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_OPEN);
            
        }

        return $this;
    }    
}

