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
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer;
class View extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

	protected $_objectManager;
	/**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,\Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {
		$this->_objectManager=$objectManager;
        parent::__construct($context, $data);
    }
	/**
	* Return the Order Id Link
	*
	*/
	public function render(\Magento\Framework\DataObject $row)
    {
		if($row->getVendorId()!=''){
			$order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($row->getOrderId()); 
			$url =  $this->getUrl('adminhtml/sales_order/view', array('order_id' => $order->getId()));
			return "<a href='". $url . "' target='_blank' >".$this->__('View')."</a>";
		}else 
    		 return "";
		} 
}