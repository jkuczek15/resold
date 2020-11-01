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

namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit\Tab;


class Addorder extends \Magento\Backend\Block\Template
{
  protected $_availableMethods = null;
  protected $_vendor;
  protected $_currencyInterface;
  protected $_urlBuilder;
  protected $_formFactory;
  protected $_vorders;
  protected $_objectManager;
  

   public function __construct(\Magento\Backend\Block\Template\Context $context,
    \Magento\Framework\ObjectManagerInterface $objectManager,
    \Ced\CsMarketplace\Model\Vendor $vendor,
    \Ced\CsMarketplace\Model\Vorders $vorders,
    \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
    \Magento\Backend\Model\Url $urlBuilder,
    \Magento\Framework\Data\FormFactory $formFactory,
    array $data = [])
    {
    $this->_objectManager = $objectManager;  
    $this->_vorders = $vorders; 
    $this->_vendor = $vendor;
    $this->_formFactory = $formFactory;
    $this->_currencyInterface = $localeCurrency;
    $this->_urlBuilder = $urlBuilder;
    parent::__construct($context, $data);
    $this->setTemplate('Ced_CsMarketplace::vpayments/edit/tab/addorder.phtml');
    
    }


  /**
     * Round price
     *
     * @param mixed $price
     * @return double
     */
    public function roundPrice($price)
    {
      return $price;
    }
  
  public function availableMethods() {
    if($this->_availableMethods == null) {
      $vendorId = $this->getRequest()->getParam('vendor_id',0);
      $this->_availableMethods = $this->_vendor->getPaymentMethodsArray($vendorId);
    }
    return $this->_availableMethods;
  }
  
  protected function _prepareLayout()
    {
       return parent::_prepareLayout();

        $this->setChild('csmarketplace_continue_button',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData(array(
                    'label'     => __('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','vendor_id')",
                    'class'     => 'save primary'
                    ))
                );
        
    }
    
  public function getContinueUrl()
    {
        return $this->_urlBuilder->getUrl('*/*/*', array(
            '_current'  => true,
            '_secure' => true,
            'vendor_id' => '{{vendor_id}}', 
        ));
    }
  
  public function getButtonsHtml()
  {
    $addButtonData = array(
        'label' => __('Add/Remove Amount(s) for Payment'),
        'onclick' => "this.parentNode.style.display = 'none'; document.getElementById('order-search').style.display = ''",
        'class' => 'add',
    );
    return $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData($addButtonData)->toHtml();
  }
  
  protected function noticeBlock() {
    if(count($this->availableMethods()) == 0) {
      return '<div>
              <ul class="messages">
                  <li class="notice-msg">
                      <ul>
                          <li>'.__("Can't continue with payment,because vendor did not specify payment method(s).").'</li>
                      </ul>
                  </li>
              </ul>
            </div>';
    }
    return '';
    
  }
  
  public function getSearchFormHtml() {
    $form = $this->_formFactory->create(); 
    $fieldset = $form->addFieldset('form_fields', array('legend'=>__('Beneficiary  Information')));
    $vendorId = $this->getRequest()->getParam('vendor_id',0);
    $message = __("Are you sure to change the vendor Because it will change the Selected Amount(s) for Payment section.");
    $params = $this->getRequest()->getParams();
    $type = isset($params['type']) && in_array($params['type'],array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?$params['type']:\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;
  
    $id = $fieldset->addField('vendor_id', 'select', array(
                'label'     => __('Beneficiary Vendor'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'vendor_id',
                'script'    => 'var cs_ok = 0;',
                'onchange'  => "document.getElementById('order-items').innerHTML=''; document.getElementById('order-search').innerHTML=''; setLocation('".$this->_urlBuilder->getUrl('*/*/new',array('type'=>$type))."vendor_id/'+this.value);",
                'value'   => $vendorId,
                'values' => $this->_vendor->getCollection()->toOptionpayArray(),
                'after_element_html' => '<small>Vendor selection will change the <b>"Selected Amount(s) for Payment"</b> section.</small>',
              ));

                
    $params = $this->getRequest()->getParams();
    
    $type = isset($params['type']) && in_array(trim($params['type']),array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?trim($params['type']):\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;

    $relationIds = isset($params['order_ids'])? explode(',',trim($params['order_ids'])):array();
    $collection = $this->_vorders
              ->getCollection()
              ->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
    if($type == \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT) {
      $collection->addFieldToFilter('order_payment_state',array('eq'=>\Magento\Sales\Model\Order\Invoice::STATE_PAID))
             ->addFieldToFilter('payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_REFUND));
    } else{
      $collection->addFieldToFilter('order_payment_state',array('eq'=>\Magento\Sales\Model\Order\Invoice::STATE_PAID))
             ->addFieldToFilter('payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_OPEN));
    }

    if(isset($relationIds) && !empty($relationIds)){
      $collection = $collection->addFieldToFilter('id',array('in'=>$relationIds)); 
    }    

    $renderer = $this->_objectManager->get('Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Orderid');

    $collection->getSelect()->columns(array('net_vendor_earn' =>
     new \Zend_Db_Expr('(base_order_total - shop_commission_base_fee)')));
    $html="";   
    $html.='<div class="entry-edit">
          <div class="entry-edit-head">
            <div id="csmarketplace_add_more" style="float: right;">'.$this->getButtonsHtml().'</div>
            <h4 class="icon-head head-cart">'.__("Selected Amount(s) for Payment").'</h4>
          </div>
          <div class="grid" id="order-items_grid">
            <table cellspacing="0" class="data order-tables">
     
              <col width="100" />
              <col width="40" />
              <col width="100" />
              <col width="80" />
              <thead>
                <tr class="headings">
                  <th class="no-link" style="text-align:left;">'.__("Order ID").'</th>
                  <th class="no-link" style="text-align:left;">'.__("Grand Total") .'</th>
                  <th class="no-link" style="text-align:left;">'.__("Commission Fee").'</th>
                  <th class="no-link" style="text-align:left;">'.__("Vendor Payment").'</th>
                </tr>
              </thead>
              <tbody>';
    $amount = 0.00;
    $class = '';
    foreach ($collection as $item)
    {
      $class = ($class == 'odd')? 'even':'odd';
      $html .= '<tr class="'.$class.'"';
      $html.='>';

      $html .= '<td>'.$renderer->render($item).'</td>';
      $html .= '<td>'.$this->_currencyInterface
                    ->getCurrency($item->getBaseCurrency())
                    ->toCurrency($item->getBaseOrderTotal()).'</td>';
      $html .= '<td>'.$this->_currencyInterface
                    ->getCurrency($item->getBaseCurrency())
                    ->toCurrency($item->getShopCommissionBaseFee()).'</td>';
      $html .= '<td>'.$this->_currencyInterface
                    ->getCurrency($item->getBaseCurrency())
                    ->toCurrency($item->getNetVendorEarn());
          
      $amount += $item->getNetVendorEarn();
      $html .= '<input id="csmarketplace_vendor_orders_'.$item->getId().'" type="hidden" value="'.$this->roundPrice($item->getNetVendorEarn()).'" name="orders['.$item->getOrderId().']"/>';
          
      $html .= '</td>';
      $html .= '</tr>';
      
    } 
     
    $html.=       ' </tbody></table>
             </div>
    </div>';            

  
    $fieldset->addField('csmarketplace_vendor_total', 'text', array(
                'label'     => __('Total Amount'),
                'class'     => 'required-entry validate-greater-than-zero',
                'required'  => true,
                'name'      => 'total',
                'value'   => $this->roundPrice($amount),
                'readonly'  => 'readonly',
                'after_element_html' => '<b>['.$this->_objectManager->get('Magento\Directory\Helper\Data')->getBaseCurrencyCode().']</b><small><i> Readonly field</i>.</small>',
                ));
    return array($this->noticeBlock().$form->toHtml(),$html);
  }
  
  public function getAddOrderBlock() {
    $relationIds=$this->getRequest()->getParam('order_ids',array());
    $vendorId = $this->getRequest()->getParam('vendor_id',0);
    $params = $this->getRequest()->getParams();
    $type = isset($params['type']) && in_array($params['type'],array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?$params['type']:\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;
    $collection = $this->_vorders
              ->getCollection()
              ->addFieldToFilter('vendor_id',array('eq'=>$vendorId));
    if($type == \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT) {
      $collection->addFieldToFilter('order_payment_state',array('eq'=>\Magento\Sales\Model\Order\Invoice::STATE_PAID))
             ->addFieldToFilter('payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_REFUND));
    } else {
      $collection->addFieldToFilter('order_payment_state',array('eq'=>\Magento\Sales\Model\Order\Invoice::STATE_PAID))
             ->addFieldToFilter('payment_state',array('eq'=>\Ced\CsMarketplace\Model\Vorders::STATE_OPEN));
    }
    $collection = $collection->addFieldToFilter('id',array('in'=>$relationIds)); 
  
    $renderer = $this->_objectManager->get('Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Orderid');
    $collection->getSelect()->columns(array('net_vendor_earn' => new \Zend_Db_Expr('(base_order_total - shop_commission_base_fee)')));
    $html="";
    $html.='<table cellspacing="0" class="data order-tables">
         
                    <col width="100" />
                    <col width="40" />
                    <col width="100" />
                    <col width="80" />
                    <thead>
                        <tr class="headings">
                    <th class="no-link" style="text-align:left;">'.__("Order ID").'</th>
                    <th class="no-link" style="text-align:left;">'.__("Grand Total") .'</th>
                    <th class="no-link" style="text-align:left;">'.__("Commission Fee").'</th>
                    <th class="no-link" style="text-align:left;">'.__("Vendor Payment").'</th>
                        </tr>
                    </thead>
                <tbody>';
    $amount = 0.00;
    $class = '';
    foreach ($collection as $item) {
      $class = ($class == 'odd')? 'even':'odd';
      
      $html .= '<tr class="'.$class.'"';
      $html.='>';

      $html .= '<td>'.$renderer->render($item).'</td>';
      $html .= '<td>'.$this->_currencyInterface
                                        ->getCurrency($item->getBaseCurrency())
                      ->toCurrency($item->getBaseOrderTotal()).'</td>';
      $html .= '<td>'.$this->_currencyInterface
                                        ->getCurrency($item->getBaseCurrency())
                      ->toCurrency($item->getShopCommissionBaseFee()).'</td>';
      $html .= '<td>'.$this->_currencyInterface
                                        ->getCurrency($item->getBaseCurrency())
                      ->toCurrency($item->getNetVendorEarn());

      $amount += $item->getNetVendorEarn();
      $html .= '<input id="csmarketplace_vendor_orders_'.$item->getId().'" type="hidden" value="'.$this->roundPrice($item->getNetVendorEarn()).'" name="orders['.$item->getOrderId().']"/>';
          
      $html .= '</td>';
      $html .= '</tr>';
      
    } 
    $html.= '<input type="hidden" id="csmarketplace_fetched_total" value="'.$this->roundPrice($amount).'"/></tbody></table>';
    return $html;
  }
}
