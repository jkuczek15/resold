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

namespace Ced\CsOrder\Block\Order;

class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    
    protected $_helperData;

    /**
     * View constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Ced\CsOrder\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Ced\CsOrder\Helper\Data $helperData,
        array $data = []
    ) {
        $this->_helperData = $helperData; 
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
        $this->setData('area', 'adminhtml');

    }
    
    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getOrder() && $this->getOrder()->getBackUrl()) {
            return $this->getOrder()->getBackUrl();
        }
        return $this->_urlBuilder->getUrl('csorder/*/');
    }

     /**
     * Constructor
     *
     * @return                                        void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'order_id';
        $this->_controller = 'order';
        $this->_mode = 'view';
        $this->_blockGroup = 'Ced_CsOrder';

        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->buttonList->remove('order_edit');
        $this->buttonList->remove('order_cancel');
        $this->buttonList->remove('void_payment');
        $this->buttonList->remove('send_notification');
        $this->buttonList->remove('order_hold');
        $this->buttonList->remove('order_unhold');
        $this->buttonList->remove('accept_payment');
        $this->buttonList->remove('deny_payment');
        $this->buttonList->remove('get_review_payment_update');
        $this->buttonList->remove('order_reorder');
        $this->buttonList->remove('order_creditmemo');
        $this->buttonList->remove('order_ship');
        $this->buttonList->remove('order_invoice');
        
       
        $this->setId('sales_order_view');
        $order = $this->getOrder();
        $vorder = $this->getVorder();
        
        if (!$order) {
            return;
        }

        if ($this->_helperData->canCreateCreditmemoEnabled($vorder) && $vorder->canCreditmemo()) {
            $message = __(
                'This will create an offline refund. ' .
                'To create an online refund, open an invoice and create credit memo for it. Do you want to continue?'
            );
            $onClick = "setLocation('{$this->getCreditmemoUrl()}')";
            if ($order->getPayment()->getMethodInstance()->isGateway()) {
                $onClick = "confirmSetLocation('{$message}', '{$this->getCreditmemoUrl()}')";
            }
            $this->buttonList->add(
                'order_creditmemo',
                ['label' => __('Credit Memo'), 'onclick' => $onClick, 'class' => 'credit-memo']
            );
        }

  
        if ($this->_helperData->canCreateInvoiceEnabled($vorder) && $vorder->canInvoice()) {
        	
            $_label = $order->getForcedShipmentWithInvoice() ? __('Invoice and Ship') : __('Invoice');
            $this->buttonList->add(
                'order_invoice',
                [
                    'label' => $_label,
                    'onclick' => 'setLocation(\'' . $this->getInvoiceUrl() . '\')',
                    'class' => 'btn btn-info'
                ]
            );
        }
   
        if ($this->_helperData->canCreateShipmentEnabled($vorder) &&  $vorder->canShip() && !$order->getForcedShipmentWithInvoice()
        ) {
            $this->buttonList->add(
                'order_ship',
                [
                    'label' => __('Ship'),
                    'onclick' => 'setLocation(\'' . $this->getShipUrl() . '\')',
                    'class' => 'ship'
                ]
            );
        }

    
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getVorder()
    {
        return $this->_coreRegistry->registry('current_vorder');
    }
    
    /**
     * Invoice URL getter
     *
     * @return string
     */
    public function getInvoiceUrl()
    {
        return $this->getUrl('csorder/invoice/new', array('_secure'=>true));
    }
    
    public function getShipUrl()
    {
        return $this->getUrl('csorder/shipment/new', array('_secure'=>true));
    }
    
    public function getCreditmemoUrl()
    {
        return $this->getUrl('*/creditmemo/new', array('_secure'=>true));
    }
    
    /**
     * URL getter
     *
     * @param  string $params
     * @param  array  $params2
     * @return string
     */
    public function getUrl($params = '', $params2 = [])
    {
        $params2['order_id'] = $this->getRequest()->getParam('order_id');
        return $this->_urlBuilder->getUrl($params, $params2);
    }
    
    
    protected function _isAllowedAction($action)
    {
        return true;
    }
}
