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
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsOrder\Model;
use Magento\Framework\Api\AttributeValueFactory;
class Creditmemo extends \Ced\CsMarketplace\Model\FlatAbstractModel
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
    
    protected $_eventPrefix      = 'csorder_creditmemo';
    protected $_eventObject      = 'vcreditmemo';
    public $_vendorstatus=null;
    protected $customerSession;
    protected $_dataHelper;
    protected $_aclHelper;
    protected $_objectManager;
    protected $_storeManager;
    protected $regionFactory;

    /**
     * Creditmemo constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {


        $this->_objectManager=$objectInterface;
        $this->_storeManager = $storeManager;
        $this->regionFactory = $regionFactory;

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
        $this->_init('Ced\CsOrder\Model\ResourceModel\Creditmemo');
    }

    /**
     * @param $creditmemo
     * @return mixed
     */
    public function updateTotal($creditmemo)
    {

            $vorder = $this->_objectManager->get("Ced\CsOrder\Model\Vorders")->setVendorId($this->getVendorId())->getVorderByCreditmemo($creditmemo);
           
        if (!$this->_registry->registry('current_vorder')) {

            $this->_registry->register('current_vorder', $vorder);
        }
       
        
        if(!$vorder->isAdvanceOrder() && $vorder->getShippingAmount()>=0) {
                $creditmemo->setOrder($vorder->getOrder(false, true));
            if($creditmemo->getShippingAmount()>=0) {
                $creditmemo->setShippingAmount($vorder->getShippingAmount());
                $creditmemo->setBaseShippingAmount($vorder->getBaseShippingAmount());

            }
            
                $subtotal = $this->getItemSubtotalByCreditmemo($creditmemo);
                $baseSubtotal = $this->getItemBaseSubtotalByCreditmemo($creditmemo);

                $discount = $this->getItemDiscountByCreditmemo($creditmemo);
                $baseDiscount = $this->getItemBaseDiscountByCreditmemo($creditmemo);
                $creditmemo->setSubtotal($subtotal);
                $creditmemo->setBaseSubtotal($baseSubtotal);
                $tax = $this->getItemTaxByCreditmemo($creditmemo);
                $baseTax = $this->getItemBaseTaxByCreditmemo($creditmemo);
                $creditmemo->setTaxAmount($tax);
                $creditmemo->setBaseTaxAmount($baseTax);
                $creditmemo->setDiscountAmount($discount);
                $creditmemo->setBaseDiscountAmount($baseDiscount);
                $adjustment = $creditmemo->getAdjustment();
                $grandTotal = $subtotal -$discount
                                + $tax 
                                //+ $adjustmentPositive
                                + $adjustment
                                + $creditmemo->getShippingAmount();
                                
                $baseGrandTotal = $baseSubtotal -$baseDiscount
                                + $baseTax 
                                //+ $adjustmentPositive
                                + $adjustment
                                + $creditmemo->getBaseShippingAmount();
                                
                $creditmemo->setGrandTotal($grandTotal);
                 $creditmemo->setBaseGrandTotal($baseGrandTotal);
                
        }   
            
            
        if(!$this->_objectManager->get('Ced\CsOrder\Helper\Data')->canShowShipmentBlock($vorder)) {
            
            $creditmemo->setShippingAmount(0);
            $creditmemo->setBaseShippingAmount(0);
            $subtotal = $this->getItemSubtotalByCreditmemo($creditmemo);
                $baseSubtotal = $this->getItemBaseSubtotalByCreditmemo($creditmemo);

                $discount = $this->getItemDiscountByCreditmemo($creditmemo);
                $baseDiscount = $this->getItemBaseDiscountByCreditmemo($creditmemo);
                $creditmemo->setSubtotal($subtotal);
                $creditmemo->setBaseSubtotal($baseSubtotal);
                $tax = $this->getItemTaxByCreditmemo($creditmemo);
                $baseTax = $this->getItemBaseTaxByCreditmemo($creditmemo);
                $creditmemo->setTaxAmount($tax);
                $creditmemo->setBaseTaxAmount($baseTax);
                $creditmemo->setDiscountAmount($discount);
                $creditmemo->setBaseDiscountAmount($baseDiscount);
                $adjustment = $creditmemo->getAdjustment();
                $grandTotal = $subtotal -$discount
                                + $tax 
                                //+ $adjustmentPositive
                                + $adjustment
                                + $creditmemo->getShippingAmount();
                                
                $baseGrandTotal = $baseSubtotal -$baseDiscount
                                + $baseTax 
                                //+ $adjustmentPositive
                                + $adjustment
                                + $creditmemo->getBaseShippingAmount();
                                
                $creditmemo->setGrandTotal($grandTotal);
                 $creditmemo->setBaseGrandTotal($baseGrandTotal);
        }
            
            
            
            return $creditmemo;
    }

    /**
     * @param $creditmemo
     * @return int
     */
    public function getItemSubtotalByCreditmemo($creditmemo)
    {
            $items = $creditmemo->getAllItems();
            $vendorId = $this->getVendorId();
            $total = 0;
        foreach($items as $_item){
            
          
            $vendorIdProductId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ($vendorIdProductId!=$vendorId ) {
                continue; 
            }
            $total += $_item->getRowTotal();
        }
            return $total;
    }

    /**
     * @param $creditmemo
     * @return int
     */
    public function getItemBaseSubtotalByCreditmemo($creditmemo)
    {
            $items = $creditmemo->getAllItems();
            $vendorId = $this->getVendorId();
            $total = 0;
        foreach($items as $_item){
            
          
           $vendorIdProductId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ($vendorIdProductId!=$vendorId ) {
                continue; 
            }
            $total += $_item->getBaseRowTotal();
        }
            return $total;
    }

    /**
     * @param $creditmemo
     * @return int
     */
    public function getItemTaxByCreditmemo($creditmemo)
    {
            $items = $creditmemo->getAllItems();
            $vendorId = $this->getVendorId();
            $total = 0;
        foreach($items as $_item){
            $vendorIdProductId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ($vendorIdProductId!=$vendorId ) {
                continue; 
            }
            $total += $_item->getTaxAmount();
        }
            return $total;
    }

    /**
     * @param $creditmemo
     * @return int
     */
    public function getItemBaseTaxByCreditmemo($creditmemo)
    {
            $items = $creditmemo->getAllItems();
            $vendorId = $this->getVendorId();
            $total = 0;
        foreach($items as $_item){
            $vendorIdProductId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ($vendorIdProductId!=$vendorId ) {
                continue; 
            }
            $total += $_item->getBaseTaxAmount();
        }
            return $total;
    }

    /**
     * @param $creditmemo
     * @return int
     */
    public function getItemDiscountByCreditmemo($creditmemo)
    {

        $items = $creditmemo->getAllItems();
        $vendorId = $this->getVendorId();
        $total = 0;
        foreach($items as $_item){
            $vendorIdProductId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ($vendorIdProductId!=$vendorId ) {
                continue; 
            }
            $total += $_item->getDiscountAmount();
        }

        return $total;
    }

    /**
     * @param $creditmemo
     * @return int
     */
    public function getItemBaseDiscountByCreditmemo($creditmemo)
    {

        $items = $creditmemo->getAllItems();
        $vendorId = $this->getVendorId();
        $total = 0;
        foreach($items as $_item){
            $vendorIdProductId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ($vendorIdProductId!=$vendorId ) {
                continue; 
            }
            $total += $_item->getBaseDiscountAmount();
        }

        return $total;
    }
    
}
