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

 
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit;
 
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
 	protected $_directoryHelper;
     
    public function __construct(
    		\Magento\Backend\Block\Template\Context $context,
    		\Magento\Framework\Registry $registry,
    		\Magento\Framework\Data\FormFactory $formFactory,
    		\Magento\Directory\Helper\Data $directoryHelper,
    		array $data = []
    ) {
    	parent::__construct($context, $registry,$formFactory,$data);
    	$this->_directoryHelper = $directoryHelper;
    	$this->_coreRegistry = $registry;
    }
    
	protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
        $this->setTitle(__('Vendor Information'));
    }

	protected function _prepareForm()
	{
		$form = $this->_formFactory->create([
						'data' => [
                                'id' => 'edit_form',
                                'action' => $this->getUrl('*/*/save', ['vendor_id' => $this->getRequest()->getParam('vendor_id')]),
                                'method' => 'post',
        						'enctype' => 'multipart/form-data',
                        ],
					]	
				);

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
	/**
	 * Get form HTML
	 *
	 * @return string
	 */
	public function getFormHtml()
	{
		if (is_object($this->getForm())) {
		$html ='';
		
		$html .= "<script type=\"text/javascript\">" .
				"require(['mage/adminhtml/form'], function(){" .
				"window.updater = new RegionUpdater('country_id'," .
				" 'region', 'region_id', " .
				$this->_directoryHelper->getRegionJson() .
				", 'disable');});</script>";
		
		$html.="<script>require(['jquery','jquery/ui'
								   ], function($){
				                
										  $( document ).ready(function() {
				
										    var country_id = document.getElementById('country_id').value;
										    var rurl ='".$this->getUrl('*/*/country',array('_nosid'=>true))."';
										    var formkey = '".$this->getFormKey()."';
										    $.ajax({
												url: rurl,
												type: 'POST',
												data: {cid:country_id,form_key:formkey},
												dataType: 'html',
												success: function(stateform) {
										    		 stateform =  JSON.parse(stateform);
													 if(stateform=='true'){
										          		 document.getElementById('region').parentNode.parentNode.style.display='none';
										          		 document.getElementById('region_id').parentNode.parentNode.style.display='block';
										        	   }else{
										          		 document.getElementById('region_id').parentNode.parentNode.style.display='none'; 
										          		 document.getElementById('region').parentNode.parentNode.style.display='block'; 
										         		}
												}
										    });
										var element = document.getElementById('region_id');
										if(element){
										  if($(element).is(':visible')){
										     element.value = '".$this->_coreRegistry->registry('vendor_data')->getRegionId()."';
									          }else{
		                                        	                         setTimeout(function(){ element.value = '".$this->_coreRegistry->registry('vendor_data')->getRegionId()."';
										    }, 5000);
		                                                                    }
										  }										    
									   	 });
									   	country_id.onchange = function() {
										    var country_id = document.getElementById('country_id').value;
										    var rurl ='".$this->getUrl('*/*/country',array('_nosid'=>true))."';
										    $.ajax({
												url: rurl,
												type: 'POST',
												data: {cid:country_id},
												dataType: 'html',
												success: function(stateform) {
										    		stateform =  JSON.parse(stateform);
													 if(stateform=='true'){
										          		document.getElementById('region').parentNode.parentNode.style.display='none';
										          		document.getElementById('region_id').parentNode.parentNode.style.display='block';
										        	   }else{
										          		 document.getElementById('region_id').parentNode.parentNode.style.display='none'; 
										          		 document.getElementById('region').parentNode.parentNode.style.display='block'; 
										         		}
												}
										    });
									   	}; 
										   	
								   });
								</script>";
			return $this->getForm()->getHtml().$html;
		}
		return '';
	}
}
