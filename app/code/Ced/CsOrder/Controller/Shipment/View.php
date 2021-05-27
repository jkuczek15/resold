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
namespace Ced\CsOrder\Controller\Shipment; 
 
class View extends \Ced\CsMarketplace\Controller\Vendor
{


    /**
     * 
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $vendorId = $this->session->getVendorId();
        $coreRegistry=$this->_objectManager->get('Magento\Framework\Registry');
        $Shipment_id = $this->getRequest()->getParam('shipment_id');
        if($Shipment_id) {
            $shipment=$this->_objectManager->get('Magento\Sales\Model\Order\Shipment')->load($Shipment_id);
            $coreRegistry->register('current_shipment', $shipment, true);
            $vorder = $this->_objectManager->get("Ced\CsOrder\Model\Vorders")->setVendorId($vendorId)->getVorderByShipment($shipment);
            $coreRegistry->register('current_vorder', $vorder, true);        
            
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Shipment').' # '.$shipment->getIncrementId());
            return $resultPage;
        }else{
            return $this->messageManager->addError(__('Shipment Does not exists.'));
        }   
    }
}
 
