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
 
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity;
 
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

	 /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
	
	/**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\ObjectManagerInterface $objectInterface,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
		$this->_objectManager = $objectInterface;
        parent::__construct($context, $data);
    }

    public function _construct()
    {                        
        $this->_objectId = 'vendor_id';
        $this->_blockGroup = 'Ced\CsMarketplace';
        $this->_controller = 'adminhtml_vendor_entity';
        		
		parent::_construct();
		
		$this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [ 'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']], ]
            ],
            10
        );
		
		
        $this->buttonList->update('save', 'label', __('Save Vendor'));
		$this->buttonList->update('delete', 'label', __('Delete Vendor'));
        
        if( $this->_coreRegistry->registry('vendor_data') && $this->_coreRegistry->registry('vendor_data')->getId() ) {
	        $vendorId = $this->_coreRegistry->registry('vendor_data')->getId();
	        $url = '';
	        $button = '';
	        $class = '';
	        $model = $this->_objectManager->get('Ced\CsMarketplace\Model\Vshop')->loadByField(array('vendor_id'),array($vendorId));
	        
	        if($model->getId()!='' && $model->getShopDisable() == \Ced\CsMarketplace\Model\Vshop::ENABLED){
	        	$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $vendorId, 'shop_disable'=> \Ced\CsMarketplace\Model\Vshop::DISABLED, 'inline'=>1));
	        	$url = "deleteConfirm('".__('Are you sure you want to Disable?')."','".$url."')";
	        	$button = __('Disable Vendor Shop');
	        	$class = 'delete';
	        }
	        else if($model->getId()!='' && $model->getShopDisable() == \Ced\CsMarketplace\Model\Vshop::DISABLED) {
	        	$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $vendorId, 'shop_disable'=> \Ced\CsMarketplace\Model\Vshop::ENABLED, 'inline'=>1));
	        	$url = "deleteConfirm('".__('Are you sure you want to Enable?')."','".$url."')";
	        	$button = __('Enable Vendor Shop');
	        	$class = 'save';
	        }
	        else{
	        	$url =  $this->getUrl('*/*/massDisable', array('vendor_id' => $vendorId, 'shop_disable'=> \Ced\CsMarketplace\Model\Vshop::DISABLED, 'inline'=>1));
	        	$url = "deleteConfirm('".__('Are you sure you want to Disable?')."','".$url."')";
	        	$button = __('Disable Vendor Shop');
	        	$class = 'delete';
	        }
	        $this->buttonList->add('shop_disable', [
	        		'label'     => $button,
	        		'onclick'   => $url,
	        		'class'     => $class,
	        ], -100);
        }
    }

	
	
	 /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $vendor = $this->_coreRegistry->registry('vendor_data');
        if ($rule->getVendorId()) {
            return __("Edit '%1'", $this->escapeHtml($vendor->getName()));
        } else {
            return __('Add Vendor');
        }
    }

    /**
     * Retrieve products JSON
     *
     * @return string
     */
    public function getProductsJson()
    {
        return '{}';
    }
	
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/CheckAvailability', ['_current' => true]);
    }
}
