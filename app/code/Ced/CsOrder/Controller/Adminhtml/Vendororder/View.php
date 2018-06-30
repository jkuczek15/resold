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
namespace Ced\CsOrder\Controller\Adminhtml\Vendororder;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class View extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @param null $orderId
     * @param bool $viewMode
     * @return bool
     */
    protected function _loadValidOrder($orderId = null, $viewMode = false)
    {

        $register = $this->_objectManager->get('Magento\Framework\Registry');



        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('vorder_id', 0);
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
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->loadByField(array('order_id','vendor_id'), array($incrementId,$vendorId));
        }

        //add view mode for shipping method
        $order = $vorder->getOrder(false, $viewMode);


            $register->register('current_order', $order);
            $register->register('sales_order', $order);
            $register->register('current_vorder', $vorder);
            return true;
    }
     /**
     * Check order view availability
     *
     * @param  Mage_Sales_Model_Order $order
     * @return bool
     */
   


    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    
    /**
     * View action
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $register = $this->_objectManager->get('Magento\Framework\Registry');
        if (!$this->_loadValidOrder(null, true)) {
            return false;
        }
        $order = $register->registry('current_order');
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_CsMarketplace::csmarketplace');
        $resultPage->addBreadcrumb(__('CsMarketplace'), __('CsOrder'));
        $resultPage->addBreadcrumb(__('Vendor Order'), __('Vendor Order'));
        $resultPage->getConfig()->getTitle()->prepend(__('Order #'.$order->getIncrementId()));
        return $resultPage;
    }
}
