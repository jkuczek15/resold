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


namespace Ced\CsVendorReview\Block\Adminhtml\Rating\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
   
    protected $_systemStore;

    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('csvendorreview_rating');
        $isElementDisabled = false;
        
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
             
        
        $fieldset->addField(
            'rating_label',
            'text',
            [
                'name' => 'rating_label',
                'label' => __('Rating Label'),
                'title' => __('Rating Label'),
                'required' => true,
                'class' => 'validate-text'
                
            ]
        );
        if ($model->getId()) {
            $fieldset->addField(
                'rating_code',
                'text',
                [
                'name' => 'rating_code',
                'label' => __('Rating Code'),
                'title' => __('Rating Code'),
                'required' => true,
                'readonly' => true,
                'class' => 'validate-text'
                
                ]
            );
        } else {
            $fieldset->addField(
                'rating_code',
                'text',
                [
                'name' => 'rating_code',
                'label' => __('Rating Code'),
                'title' => __('Rating Code'),
                'required' => true,
                'class' => 'validate-text'
                
                ]
            );
        }
                         
        
        
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'validate-number validate-zero-or-greater'
                
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
}
