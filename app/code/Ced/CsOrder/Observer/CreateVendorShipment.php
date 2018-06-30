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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

Class CreateVendorShipment implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request,
        ManagerInterface $messageManager,
        \Ced\CsOrder\Helper\Data $helper,
        \Ced\CsMarketplace\Helper\Data $marketplacehelper,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Ced\CsOrder\Model\Shipment $shipment
    ) {
    
        $this->_objectManager = $objectManager;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->marketplacehelper = $marketplacehelper;
        $this->vproducts = $vproducts;
        $this->shipment = $shipment;
    }
    /**
     *Set vendor name and url to product incart
  *
     *@param $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if($this->helper->isActive()) {
                $shipment = $observer->getShipment();
                $allItems = $shipment->getAllItems();
                $shipmentVendor = [];
                foreach($allItems as $item){
                    $vendorId = $this->vproducts->getVendorIdByProduct($item->getProductId());
                    $shipmentVendor[$vendorId] = $vendorId;
                }
                foreach($shipmentVendor as $vendorId){
                    try{
                        $id = $shipment->getId();
                        $vshipment = $this->shipment;
                        $vshipment->setShipmentId($id);
                        $vshipment->setVendorId($vendorId);
                        $vshipment->save();
                    }catch(\Exception $e){
                        $e->getMessage();
                    }
                }
            }
        }catch(\Exception $e) {
            $this->marketplacehelper->logException($e);
        }
    }
} 
