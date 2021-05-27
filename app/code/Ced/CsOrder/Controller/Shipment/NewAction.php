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

class NewAction extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;


    
    /**
     * Shipment create page
     *
     * @return void
     */
    public function execute()
    {
        $csOrderHelper = $this->_objectManager->get('Ced\CsOrder\Helper\Data');
        $this->registry = $this->_objectManager->get('Magento\Framework\Registry');
        //$order = $vorder->getOrder();
        try{

            $vorderId = $this->getRequest()->getParam('order_id');
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($vorderId);
            $orderId = $vorder->getOrder()->getId();

            if(!$csOrderHelper->canCreateShipmentEnabled($vorder)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Not allowed to create Shipment.'));
            }

            $this->registry->register("current_vorder", $vorder);
            $shipmentLoader=$this->_objectManager->get('Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader'); 
            $shipmentLoader->setOrderId($orderId);
            $shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $shipmentLoader->setShipment($this->getRequest()->getParam('shipment'));
            $shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $shipmentLoader->load();
            if ($shipment) {
                $comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
                if ($comment) {
                    $shipment->setCommentText($comment);
                }

                $this->_view->loadLayout();
                // $this->_setActiveMenu('Ced_CsOrder::csorder_order');
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Shipments'));
                $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Shipment'));
                 $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Shipment').' # '.$shipment->getIncrementId());
            return $resultPage;
                //$this->_view->renderLayout();
            } else {
                $this->_redirect('*/order/view', ['order_id' => $vorderId]);
            } 
        }catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addError($exception->getMessage());
            $this->_redirectToOrder($vorderId);
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception, 'Cannot create an Shipment.');
            
            $this->_redirectToOrder($vorderId);
        }
    }
    
    /**
     * Redirect to order view page
     *
     * @param  int $orderId
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _redirectToOrder($orderId)
    {
        /**
         *
         *
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('csorder/vorders/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }
}
