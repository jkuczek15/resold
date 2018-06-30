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
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class Paymentinformation extends Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

  protected $_availableMethods = null;
  protected $_vendor;
  protected $_directoryHelper;
  
    

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Ced\CsMarketplace\Model\Vendor $vendor,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = []
    ) {
      $this->_vendor = $vendor;
      $this->_directoryHelper = $directoryHelper;
      parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
    $params = $this->getRequest()->getParams();
    $type = isset($params['type']) && in_array($params['type'],array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?$params['type']:\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;
        $form = $this->_formFactory->create();
        $this->setForm($form);
        $fieldset = $form->addFieldset('form_fields',        
           array('legend'=>__('Transaction Information')));
        $vendorId = $this->getRequest()->getParam('vendor_id',0);
    $base_amount = $this->getRequest()->getPost('total',0);
   
    $amountDesc = $this->getRequest()->getPost('orders');
    
    $vendor = $this->_vendor->getCollection()->toOptionArray();
    $ascn = isset($vendor[$vendorId])?$vendor[$vendorId]:'';
    $fieldset->addField('vendor_id', 'hidden', array(
      'name'      => 'vendor_id',
      'value'     => $vendorId,
    ));
    $fieldset->addField('amount_desc', 'hidden', array(
      'name'      => 'amount_desc',
      'value'     => json_encode($amountDesc),
    ));
    
    $fieldset->addField('test', 'hidden', array(
    		'name'      => 'test',
    		'label'    =>  __('Test'),
    		'after_element_html'    =>'<script type="text/javascript">
                                            require(["jquery"], function($){
                                                 $("#payment_code").change(function () {
                                                     var payment_code = $("#payment_code").val();
                          
                                                   $("#test").val(payment_code)  ;
                                                     });
                                            });
                                      </script>',
    ));
    
    
    $fieldset->addField('currency', 'hidden', array(
        'name'      => 'currency',
        'value'     => $this->_directoryHelper->getBaseCurrencyCode(),
    ));
    $fieldset->addField('vendor_name', 'label', array(
      'label' => __('Vendor'), 
      'after_element_html' => '<a target="_blank" href="'.$this->getUrl('csmarketplace/adminhtml_vendor/edit/',array('vendor_id'=>$vendorId, '_secure'=>true)).'" title="'.$ascn.'">'.$ascn.'</a>',
    ));
      
    $fieldset->addField('base_amount', 'text', array(
        'label'     => __('Amount'),
        'class'     => 'required-entry validate-greater-than-zero',
        'required'  => true,
        'name'      => 'base_amount',
        'value'   => $base_amount,
        'readonly'  => 'readonly',
        'after_element_html' => '<b>['.$this->_directoryHelper->getBaseCurrencyCode().']</b><small><i>'.__('Readonly field').'</i>.</small>',
      ));
      
    $fieldset->addField('payment_code', 'select', array(
      'label'     => __('Payment Method'),
      'class'     => 'required-entry',
      'required'  => true,
      'onchange'  => !$type?'vpayment.changePaymentDatail(this)':'vpayment.changePaymentToOther(this)',
      'name'      => 'payment_code',
      'values' => $this->_vendor->getPaymentMethodsArray($vendorId),
      'after_element_html' => '<small id="beneficiary-payment-detail">'.__('Select Payment Method').'</small><script type="text/javascript">var vpayment = "'.$this->getUrl("*/*/getdetail",array("vendor_id"=>$vendorId)).'";</script>',
    ));
      
    $fieldset->addField('payment_code_other', 'text', array(
          'label'     => '',
          'style'   => 'display: none;',
          'disbaled'  => 'true',
          'name'      => 'payment_code',
          'required'  => true,
          'after_element_html' =>'<script type="text/javascript">
                                              require(["jquery"], function($){
                                                   $("#payment_code").change(function () {
                                                       var payment_code = $("#payment_code").val();
                                                        $("#payment_code_other").val(payment_code)  ;
                                                       });    
                                              });
                                        </script>',
        ));
    
    $fieldset->addField('base_fee', 'text', array(
      'label'     => __('Adjustment Amount'),
      'class'     => 'validate-number',
      'required'  => false,
      'name'      => 'base_fee',
      'after_element_html' => '<b>['.$this->_directoryHelper->getBaseCurrencyCode().']</b><small>'.__('Enter adjustment amount in +/- (if any)').'</small>',
    ));
    
    
    $fieldset->addField('transaction_id', 'text', array(
      'label'     => __('Transaction Id'),
      'class'     => 'required-entry',
      'required'  => true,
      'name'      => 'transaction_id',
      'after_element_html' => '<small>'.__('Enter transaction id').'</small>',
    ));
    
    
    $fieldset->addField('textarea', 'textarea', array(
      'label'     => __('Notes'),
      'required'  => false,
      'name'      => 'notes',
    ));
      
      $form->setHtmlIdPrefix('page_');
     $htmlIdPrefix = $form->getHtmlIdPrefix();
       $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "{$htmlIdPrefix}payment_code",
                'payment_code'
            )
            ->addFieldMap(
                "{$htmlIdPrefix}payment_code_other",
                'payment_code_other'
            )
            ->addFieldDependence(
                'payment_code_other',
                'payment_code',
                'other'
            )
        );

        return parent::_prepareForm();
    
    }
    
  public function getContinueUrl()
    {
        return $this->getUrl('*/*/*', array(
            '_current'  => true,
      '_secure' => true,
            'vendor_id'       => '{{vendor_id}}'
        ));
    }
     /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('News Info');
    }
     public function getTabTitle()
    {
        return __('News Info');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
