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

 
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab;
 
class Information extends \Magento\Backend\Block\Widget\Form\Generic
{

	/* protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
        $this->setTitle(__('Vendor Information'));
    } */
	
	protected function _prepareForm(){
		$this->_objectManager=\Magento\Framework\App\ObjectManager::getInstance();
		$form = $this->_formFactory->create(); 
		$this->setForm($form);
		$model = $this->_coreRegistry->registry('vendor_data')->getData();
		$vendor_id = $this->getRequest()->getParam('vendor_id',0);
		
		$group = $this->getGroup();
		$attributeCollection = $this->getGroupAttributes();
		
		$fieldset = $form->addFieldset('group_'.$group->getId(), array('legend'=>__($group->getAttributeGroupName())));    
		
		foreach($attributeCollection as $attribute){
			$ascn = 0;
			if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
				continue;
			}
			if ($attribute->getAttributeCode()=="email" || $attribute->getAttributeCode()=="website_id") {
				continue;
			}
			
			if ($inputType = $attribute->getFrontend()->getInputType()) {
				if($vendor_id && $attribute->getAttributeCode()=="created_at") {
					$inputType = 'label';
				} elseif (!$vendor_id && $attribute->getAttributeCode()=="created_at") {
					continue;
				}
				if(!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])){ $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();  }
				 
				$showNewStatus = false;
				if($inputType == 'boolean') $inputType = 'select';
				if($attribute->getAttributeCode() == 'customer_id' && $vendor_id) {
					$options = $attribute->getSource()->toOptionArray($model[$attribute->getAttributeCode()]);
					if(count($options)) {
						$ascn = isset($options[0]['label'])?$options[0]['label']:0;
					}
				}
				
				if($attribute->getAttributeCode() == 'status') {
					$showNewStatus = true;	
				}
								
				$fieldType = $inputType;
				$rendererClass = $attribute->getFrontend()->getInputRendererClass();
				if (!empty($rendererClass)) {
					$fieldType = $inputType . '_' . $attribute->getAttributeCode();
					$form->addType($fieldType, $rendererClass);
				}

				$element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
					array(
						'name'      => "vendor[".$attribute->getAttributeCode()."]",
						'label'     => $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontend()->getLabel(),
						'class'     => $attribute->getFrontend()->getClass(),
						'required'  => $attribute->getIsRequired(),
						'note'      => $ascn && $attribute->getAttributeCode() == 'customer_id' && $vendor_id?'':$attribute->getNote(),
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'disabled':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?true:'',
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'readonly':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?true:'',
						$ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'style':'' => $ascn && ($attribute->getAttributeCode() == 'customer_id') && $vendor_id?'display: none;':'',
						'value'    => $model[$attribute->getAttributeCode()],
					)
				)
				->setEntityAttribute($attribute);
				if($ascn && $attribute->getAttributeCode() == 'customer_id' && $vendor_id) {
					$element->setAfterElementHtml('<a target="_blank" href="'.$this->getUrl('customer/index/edit',array('id'=>$model[$attribute->getAttributeCode()], '_secure'=>true)).'" title="'.$ascn.'">'.$ascn.'</a>');
				}
				else if($attribute->getAttributeCode() == 'customer_id') {
					$element->setAfterElementHtml('<a target="_blank" href="'.$this->getUrl('customer/index/edit').'" title="Create New Customer">Create New Customer</a>');
				}else if($attribute->getAttributeCode() == 'shop_url'){
					$element->setAfterElementHtml('<span class="note"><small style="font-size: 10px;">Please enter your Shop URL Key. For example "my-shop-url".</small></span>');
				}else if($element->getExtType() == 'file') {
				    if ($element->getValue()) {
					$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$element->getValue();
					$element->setAfterElementHtml('<p><a href="'.$url.'" target="_blank" >'.$element->getLabel().' Download</a></p>');
				    }
				}
				else {
					$element->setAfterElementHtml('');
				}
				if ($inputType == 'select') {
					
					$element->setValues($attribute->getSource()->getAllOptions(true,$showNewStatus));
				} else if ($inputType == 'multiselect') {
					$element->setValues($attribute->getSource()->getAllOptions(false,$showNewStatus));
					$element->setCanBeEmpty(true);
				} else if ($inputType == 'date') {
					$element->setImage($this->getViewFileUrl('images/calendar.gif'));
					$element->setFormat($this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\Timezone')->getDateFormatWithLongYear());
				} else if ($inputType == 'multiline') {
					$element->setLineCount($attribute->getMultilineCount());
				}
			}
		}

		return parent::_prepareForm();
	}
}
