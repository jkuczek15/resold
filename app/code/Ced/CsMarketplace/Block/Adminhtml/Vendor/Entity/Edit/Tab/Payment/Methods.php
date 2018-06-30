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
 
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Payment;
 
class Methods extends \Magento\Backend\Block\Widget\Form\Generic
{
	
	protected $_objectManager;
	
	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		$this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form before rendering HTML
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
		$vendor = $this->_coreRegistry->registry('vendor_data');
		if ($vendor) {
			$methods = $vendor->getPaymentMethods();
			$form = $this->_formFactory->create();
			if(count($methods) >0 ) {
				$cnt = 1;
				foreach($methods as $code => $method) {
					$fields = $method->getFields();
					if (count($fields) > 0) {
						$fieldset = $form->addFieldset('csmarketplace_'.$code, array('legend'=>$method->getLabel('label')));
						foreach ($fields as $id=>$field) {
							$key = strtolower(\Ced\CsMarketplace\Model\Vsettings::PAYMENT_SECTION.'/'.$method->getCode().'/'.$id);
							$value = '';
							if((int)$vendor->getId()){
								$setting = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings')->loadByField(array('key','vendor_id'),array($key,(int)$vendor->getId()));
								if($setting) $value = $setting->getValue();
							}
							$fieldset->addField($method->getCode().$method->getCodeSeparator().$id, 'label', array(
								'label'     							=> $method->getLabel($id),
								'value'      							=> isset($field['values'])?$this->getLabelByValue($value,$field['values']):$value,
								'name'      							=> 'groups['.$method->getCode().']['.$id.']',
								isset($field['class'])?'class':''   	=> isset($field['class'])?$field['class']:'',
								isset($field['required'])?'required':'' => isset($field['required'])?$field['required']:'',
								isset($field['onchange'])?'onchange':'' => isset($field['onchange'])?$field['onchange']:'',
								isset($field['onclick'])?'onclick':''   => isset($field['onclick'])?$field['onclick']:'',
								isset($field['href'])?'href':''			=> isset($field['href'])?$field['href']:'',
								isset($field['target'])?'target':''		=> isset($field['target'])?$field['target']:'',
								isset($field['values'])? 'values': '' 	=> isset($field['values'])? $field['values']: '',
							));
						}
						$cnt++;
					}	
				}
			}
			$this->setForm($form);
		}
		return $this;
    }
	
	/**
	 * retrieve label from value
	 * @param array
	 * @return string
	 */
	protected function getLabelByValue($value = '', $values = array()) {
		foreach($values as $key=>$option) {
			
			
			if(is_array($option)){
				if(isset($option['value']) && $option['value'] == $value && $option['label']){
					return $option['label'];
					break;
				}
			}
			else
			{
				if($key == $value && $option->getText()){
					return $option->getText();
					break;
				}
			}
		}
		return $value;
	}
}