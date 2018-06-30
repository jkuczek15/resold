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
namespace Ced\CsOrder\Observer;
use Magento\Framework\Event\ObserverInterface;
Class CreateVendorInvoice implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Ced\CsOrder\Model\Invoice $vinvoice,
        \Ced\CsMarketplace\Model\Vorders $vorders
        )
    {
        $this->vproducts = $vproducts;
        $this->_objectManager = $objectManager;
        $this->vinvoice = $vinvoice;
        $this->vorders = $vorders;
    }
    
    /**
     * Adds catalog categories to top menu
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $invoice = $observer->getInvoice();
        $allItems = $invoice->getAllItems();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $invoiceVendor = [];
                                        
        foreach($allItems as $item){
            $vendorId = $this->vproducts->getVendorIdByProduct($item->getProductId());

            $invoiceVendor[$vendorId] = $vendorId;
        }
        
        foreach($invoiceVendor as $vendorId){
                
            $vInvoice = $this->vinvoice;


            try{
                $id = $invoice->getId();
                
                $vInvoice->setInvoiceId($id);
                $vInvoice->setVendorId($vendorId);
                $vInvoice->setInvoiceOrderId($invoice->getOrderId());
                if($vInvoice->canInvoiceIncludeShipment($invoice)) {
                    if($vorder = $this->vorders->getVorderByInvoice($invoice)) {
                        $vInvoice->setShippingCode($vorder->getCode());
                        $vInvoice->setShippingDescription($vorder->getShippingDescription());
                        $vInvoice->setBaseShippingAmount($vorder->getBaseShippingAmount());
                        $vInvoice->setShippingAmount($vorder->getShippingAmount());
                    }
                }
                $vInvoice->save();
            }catch(\Exception $e){
                $e->getMessage();
            }
        }
            
        
    }

        
}
