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
 
namespace Ced\CsMarketplace\Block\Vsettings\Payment;

class Methods extends \Ced\CsMarketplace\Block\Vendor\Profile\Edit
{
	/**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
		$vendor = $this->getVendorId()?$this->getVendor():array();
		if ($vendor && $vendor->getId()) {
			$methods = $vendor->getPaymentMethods();
			$form =  $this->_objectManager->get('Magento\Framework\Data\Form');
			$form->setAction($this->getUrl('*/*/save',array('section'=>\Ced\CsMarketplace\Model\Vsettings::PAYMENT_SECTION)))
				->setId('form-validate')
				->setMethod('POST')
				->setEnctype('multipart/form-data')
				->setUseContainer(true);
			if(count($methods) >0 ) {
				$cnt = 1;
				foreach($methods as $code=>$method) {
					
					$fields = $method->getFields();
					/* print_r($fields); continue; */
					if (count($fields) > 0) {
						$fieldset = $form->addFieldset('csmarketplace_'.$code, array('legend'=>$method->getLabel('label')));
						foreach ($fields as $id=>$field) {
							$key = strtolower(\Ced\CsMarketplace\Model\Vsettings::PAYMENT_SECTION.'/'.$method->getCode().'/'.$id);
							$value = '';
							$vendor_id = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getTableKey('vendor_id');
							$key_tmp = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getTableKey('key');
							$setting = $this->_objectManager->get('Ced\CsMarketplace\Model\Vsettings')
											->loadByField(array($key_tmp,$vendor_id),array($key,(int)$vendor->getId()));
							if($setting) $value = $setting->getValue();
							$fieldset->addField($method->getCode().$method->getCodeSeparator().$id, isset($field['type'])?$field['type']:'text', array(
									'label'     												=> $method->getLabel($id),
									'value'      												=> $value,
									'name'      												=> 'groups['.$method->getCode().']['.$id.']',
									isset($field['class'])?'class':''   						=> isset($field['class'])?$field['class']:'',
									isset($field['required'])?'required':''    					=> isset($field['required'])?$field['required']:'',
									isset($field['onchange'])?'onchange':''    					=> isset($field['onchange'])?$field['onchange']:'',
									isset($field['onclick'])?'onclick':''    					=> isset($field['onclick'])?$field['onclick']:'',
									isset($field['href'])?'href':''								=> isset($field['href'])?$field['href']:'',
									isset($field['target'])?'target':''							=> isset($field['target'])?$field['target']:'',
									isset($field['values'])? 'values': '' 						=> isset($field['values'])? $field['values']: '',
									isset($field['after_element_html'])? 'after_element_html':''=> isset($field['after_element_html'])? '<div><small>'.$field['after_element_html'].'</small></div>': '',
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
}
