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

namespace Ced\CsOrder\Block\Adminhtml\Vorders;

/**
 * Adminhtml sales order view
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\View
{
    
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order';
        $this->_mode = 'view';
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');
        $this->buttonList->remove('order_reorder');
        $this->buttonList->remove('order_ship');
        $this->buttonList->remove('order_invoice');
        $this->buttonList->remove('accept_payment');
        $this->buttonList->remove('deny_payment');
        $this->buttonList->remove('order_edit');
        $this->buttonList->remove('send_notification');
        $this->buttonList->remove('void_payment');
        $this->buttonList->remove('order_creditmemo');
        $this->buttonList->remove('order_unhold');
        $this->buttonList->remove('order_cancel');

        $this->buttonList->add(
            'back',
            [
                   'label' => __('Back'),
                   'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\')',
                   'class' => 'back'
               ]
        );

    }
    
    /**
     * Return back url for view grid
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('csmarketplace/*/');
    }
}
