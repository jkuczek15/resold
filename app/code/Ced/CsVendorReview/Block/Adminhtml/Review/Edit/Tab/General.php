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
 * @package   Ced_CsVendorReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsVendorReview\Block\Adminhtml\Review\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
   
    
    protected $_systemStore;
    public $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

   
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('csvendorreview_review');
        $isElementDisabled = false;
       
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);
        

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        
        $rating_model=$this->_objectManager->create('Ced\CsVendorReview\Model\Rating');
        $rating_model_data=$rating_model->getCollection();
        $rating_item=$rating_model_data->getData();

        $fieldset->addField(
            'vendor_name',
            'text',
            [
                'name' => 'vendor_name',
                'label' => __('Vendor Name'),
                'title' => __('Vendor Name'),
                'readonly' => 'true'
                
            ]
        );
        $fieldset->addField(
            'customer_name',
            'text',
            [
                'name' => 'customer_name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
            ]
        );
        foreach ($rating_item as $key => $valu) {
            $fieldset->addField(
                $valu['rating_code'],
                'select',
                [
                'name' => $valu['rating_code'],
                'label' => __($valu['rating_label']),
                'title' => __($valu['rating_label']),
                'options'   => $this->getRatingOption(),
                                
                ]
            );
        }
        $fieldset->addField(
            'subject',
            'text',
            [
                'name' => 'subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'after_element_html'=>'<style>.admin__field.field.field-service {
				  width: 100% !important;
				  float: none !important;
				}</style>',
                
            ]
        );
        $fieldset->addField(
            'review',
            'text',
            [
                'name' => 'review',
                'label' => __('Review'),
                'title' => __('Review'),
                
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options'   => $this->getStatusOption(),
            ]
        );
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('General');
    }

    
    public function getTabTitle()
    {
        return __('General');
    }

    
    public function canShowTab()
    {
        return true;
    }

    
    public function isHidden()
    {
        return false;
    }

    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    public function getRatingOption()
    {
        return [
        '0'        => __('Please Select Option'),
        '20'    => __('1 OUT OF 5'),
        '40'    => __('2 OUT OF 5'),
        '60'    => __('3 OUT OF 5'),
        '80'    => __('4 OUT OF 5'),
        '100'    => __('5 OUT OF 5')
        ];
    }
    public function getStatusOption()
    {
        return [
        '0'    => __('Pending'),
        '1'    => __('Approved'),
        '2'    => __('Disapproved')
        ];
    }
}
