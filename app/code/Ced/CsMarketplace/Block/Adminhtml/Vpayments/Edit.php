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

namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_availableMethods = null;
    protected $_header;
    protected $_acl;
    protected $_vendor;

  
   /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Framework\HTTP\Header $header,
        \Ced\CsMarketplace\Helper\Acl $acl,
        \Ced\CsMarketplace\Model\Vendor $vendor,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_header = $header;
        $this->_acl = $acl;
        $this->_vendor = $vendor;
        parent::__construct($context, $data);
    }
 
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $amount = $this->getRequest()->getPost('total',0);
        $this->_objectId = 'paymentid';
        $this->_controller = 'adminhtml_vpayments';
        $this->_blockGroup = 'Ced_CsMarketplace';
        $url = $this->_header->getHttpReferer() && preg_match('/\/index\//i',$this->_header->getHttpReferer()) ? $this->_header->getHttpReferer()  : $this->getUrl('*/*/index');
        parent::_construct();

        $this->updateButton('back', 'onclick', "setLocation('".$url."')");
        if($amount) {
            $this->updateButton('save', 'label', __('Pay').' '.$this->_acl->getDefaultPaymentTypeLabel());
        } else {


            $this->removeButton('save');


            $this->addButton('saveandcontinue', array(
                'label'     => __('Continue'),
                'onclick'   => 'saveAndContinueEdit()',
                'class'     =>  count($this->availableMethods()) == 0?'save disabled':'save primary',
                count($this->availableMethods()) == 0?'disabled':''=> count($this->availableMethods()) == 0?true:'',
            ), -100);
            
             $this->_formScripts[] = " 
                                    function saveAndContinueEdit(){
                                       

                                        var editForm = jQuery('#edit_form');
                            editForm.attr('action',editForm.attr('action')+'back/edit/'+csaction);

                                            editForm.submit();
                                     }";
        }
    }
 
   

    public function availableMethods() {
        if($this->_availableMethods == null) {
            $vendorId = $this->getRequest()->getParam('vendor_id',0);
            $this->_availableMethods = $this->_vendor->getPaymentMethodsArray($vendorId);
        }
        return $this->_availableMethods;
    }

    public function getHeaderText()
    {
        $params = $this->getRequest()->getParams();
        $type = isset($params['type']) && in_array(trim($params['type']),array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?trim($params['type']):\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;

        if($type == \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT) {
            return __("Debit Amount");
        }else {
            return __("Credit Amount");
        }
    }
}