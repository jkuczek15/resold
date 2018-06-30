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
namespace Ced\CsOrder\Controller\Vorders; 
 
class View extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     *
     *
     * @var \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;

    /**
     * @param null $orderId
     * @param bool $viewMode
     * @return bool
     */
    protected function _loadValidOrder($orderId = null, $viewMode = false)
    {

        $register = $this->_objectManager->get('Magento\Framework\Registry');
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id', 0);
        }
        $incrementId = 0;
        if ($orderId == 0) {
            $incrementId = (int) $this->getRequest()->getParam('increment_id', 0);
            if (!$incrementId) {
                $this->_forward('noRoute');
                return false;
            }
        }

        if($orderId) {

            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($orderId);
        }
        else if($incrementId) {

            $vendorId = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')
                      ->loadByField(array('order_id', 'vendor_id'), array($incrementId,$vendorId));
        }

        //add view mode for shipping method
        $order = $vorder->getOrder(false, $viewMode);

        if ($this->_canViewOrder($vorder)) {
            $register->register('current_order', $order);
            $register->register('sales_order', $order);
            $register->register('current_vorder', $vorder);
            return true;
        } else {
            $this->_redirect('csorder/vorders');
        }
        return false;
    }

    /**
     * @param $vorder
     * @return bool
     */
    protected function _canViewOrder($vorder)
    {
        if(!$this->_getSession()->getVendorId()) { return false;
        }
        $vendorId = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
         
        $incrementId = $vorder->getOrder()->getIncrementId();
        
        
        $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getCollection();
        $collection->addFieldToFilter('id', $vorder->getId())
            ->addFieldToFilter('order_id', $incrementId)
            ->addFieldToFilter('vendor_id', $vendorId);
        
        if(count($collection)>0) {
            return true;
        }else{
            return false;
        }
    }


    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $register = $this->_objectManager->get('Magento\Framework\Registry');

        if (!$this->_loadValidOrder(null, true)) {
            return false;
        }
        $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
        $helper->readNotification($this->getRequest()->getParam('order_id', 0));
        $order = $register->registry('current_order');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Order').' # '. $order->getRealOrderId());
        return $resultPage;

    }
}
 
