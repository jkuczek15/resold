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
class CreditmemoGrid extends \Ced\CsMarketplace\Model\FlatAbstractModel
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
     * CreditmemoGrid constructor.
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
                $baseDiscount = $this->getItemBaseDiscountByCreditmemo($creditmemo);
                $creditmemo->setSubtotal($subtotal);
                $tax = $this->getItemTaxByCreditmemo($creditmemo);
                $creditmemo->setTaxAmount($tax);

                $adjustment = $creditmemo->getAdjustment();
                $grandTotal = $subtotal -$baseDiscount
                                + $tax 
                                //+ $adjustmentPositive
                                + $adjustment
                                + $creditmemo->getBaseShippingAmount();
                                


                $creditmemo->setGrandTotal($grandTotal);
                
        }   
            
            
        if(!$this->_objectManager->get('Ced\CsOrder\Helper\Data')->canShowShipmentBlock($vorder)) {
            $creditmemo->setShippingAmount(0);
            $creditmemo->setBaseShippingAmount(0);
            $subtotal = $this->getItemSubtotalByCreditmemo($creditmemo);

            $creditmemo->setSubtotal($subtotal);
            $tax = $this->getItemTaxByCreditmemo($creditmemo);
            $creditmemo->setTaxAmount($tax);

            $adjustment = $creditmemo->getAdjustment();
            $grandTotal = $subtotal -$baseDiscount
                            + $tax 
                            //+ $adjustmentPositive
                            + $adjustment
                            + $creditmemo->getBaseShippingAmount();
                                


            $creditmemo->setGrandTotal($grandTotal);
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
            $vproducts=$this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldTofilter('product_id', $_item->getProductId())->addFieldTofilter('vendor_id', $vendorId )->getData();
                      if(sizeof($vproducts)>0){
                        $total += $_item->getBaseRowTotal();
                      }
          
            /*$vendorIdProductId = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ((is_object($_item->getOrderItem()) && $_item->getOrderItem()->getParentItem()) || $vendorIdProductId!=$vendorId ) { continue; 
            }
            $total += $_item->getRowTotal();*/
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
            $vendorIdProductId = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ((is_object($_item->getOrderItem()) && $_item->getOrderItem()->getParentItem()) || $vendorIdProductId!=$vendorId ) { continue; 
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
            if ((is_object($_item->getOrderItem()) && $_item->getOrderItem()->getParentItem()) || $vendorIdProductId!=$vendorId ) { continue; 
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
            if ((is_object($_item->getOrderItem()) && $_item->getOrderItem()->getParentItem()) || $vendorIdProductId!=$vendorId ) { continue; 
            }
            $total += $_item->getBaseDiscountAmount();
        }

        return $total;
    }
    
}

