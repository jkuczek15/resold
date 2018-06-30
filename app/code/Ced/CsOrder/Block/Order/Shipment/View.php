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

namespace Ced\CsOrder\Block\Order\Shipment;

/**
 * Adminhtml shipment create
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class View extends \Magento\Shipping\Block\Adminhtml\View
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry,  $data);
        $this->setData('area','adminhtml');

    }
    protected function _construct()
    {
    	$this->_objectId = 'shipment_id';
    	$this->_mode = 'view';
    	parent::_construct();
    	$this->buttonList->remove('reset');
    	$this->buttonList->remove('delete');
    	$this->buttonList->remove('save');
    	if (!$this->getShipment()) {
    		return;
    	}
    	if ($this->getShipment()->getId()) {
    		$this->buttonList->add(
    				'print',
    				[
    				'label' => __('Print'),
    				'class' => 'save',
    				'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
    				]
    		);
    	}
    }
    /**
     * @return string
     */
    public function getBackUrl()
    {
        if($this->getRequest()->getParam('order_id')) {
             return $this->getUrl(
                 'csorder/vorders/view',
                 [
                 'order_id' => $this->getShipment() ? $this->getRequest()->getParam('order_id') : null,
                 'active_tab' => 'order_shipments'
                 ]
             );
        }else{
             return $this->getUrl(
                 'csorder/shipment/index'
             );
        }
    }
    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('csorder/shipment/print', ['shipment_id' => $this->getShipment()->getId()]);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getShipment()->getBackUrl()) {
                return $this->buttonList->update(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getShipment()->getBackUrl() . '\')'
                );
            }
            return $this->buttonList->update(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl('csorder/shipment/') . '\')'
            );
        }
        return $this;
    }
}
