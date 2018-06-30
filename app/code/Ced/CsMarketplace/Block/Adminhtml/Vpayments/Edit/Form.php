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

namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
  protected $_acl;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Ced\CsMarketplace\Helper\Acl $acl,
        array $data = []
    ) {
        $this->_acl = $acl;
        parent::__construct($context, $registry, $formFactory, $data);
    }

   protected function _prepareForm()
  {
    
    $back = $this->getRequest()->getParam('back','');
    $amount = $this->getRequest()->getPost('total',0);
    $params = $this->getRequest()->getParams();
    $type = isset($params['type']) && in_array($params['type'],array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?$params['type']:\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;
    
    if($back == 'edit' && $amount) {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                      'action' => $this->getUrl('*/*/save', array('payment_method'=>$this->_acl->getDefaultPaymentType(),'type'=>$type)),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
    } else {
     
       $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/*', array('vendor_id'=>$this->getRequest()->getParam('vendor_id'),'type'=>$type)),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
    }
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}