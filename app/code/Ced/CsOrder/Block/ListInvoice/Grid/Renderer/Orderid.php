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

namespace Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer;
class Orderid extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_objectManager;

    /**
     * Orderid constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,\Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {
        $this->_objectManager=$objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if($row->getOrderId()!='') {      
            $order =  $this->_objectManager->get("Magento\Sales\Model\Order")->loadByIncrementId($row->getOrderId());
            $orderId = $order->getId();
            $url =  $this->getUrl("sales/order/view", array('order_id' => $orderId));            
            $html='<a href="#popup" onClick="javascript:openMyPopup(\''.$url.'\')" >'.$row->getOrderId().'</a>';
            return $html;
            // return "<a href='". $url . "' target='_blank' >".$row->getOrderId()."</a>";		  
        }            
        else { 
            return ''; 
        }
    }
}
