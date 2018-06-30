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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProduct\Block;

class Product extends \Magento\Backend\Block\Widget\Container
{    
	protected $_template = 'product.phtml';
	
	/**
     * @var \Magento\Catalog\Model\Product\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    protected $_objectManager;

    /**
     * @return void
     */
	protected function _construct()
	{
		$this->_controller = 'product';
		$this->_blockGroup = 'Ced_CsProduct';
		$this->_headerText = __('Products');
		parent::_construct();
	}

    /**
     * Product constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Catalog\Model\Product\TypeFactory $typeFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
	public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Catalog\Model\Product\TypeFactory $typeFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		array $data = []
	) {
		$this->_productFactory = $productFactory;
        $this->_typeFactory = $typeFactory;
        $this->_objectManager = $objectManager;
		
		parent::__construct($context, $data);
	}
	
	protected function _prepareLayout()
	{
		$addButtonProps = [
            'id' => 'add_new_product',
            'label' => __('Add Product'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Ced\CsProduct\Block\Widget\Button\SplitButton',
            'options' => $this->_getAddProductButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps); 
		
		$this->setChild(
				'grid',
				$this->getLayout()->createBlock('Ced\CsProduct\Block\Product\Grid', 'ced.csproduct.vendor.product.grid')
		); 	
		
		return parent::_prepareLayout(); 
		
	}
	/**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = [];
        $types = $this->_typeFactory->create()->getTypes();
        uasort(
            $types,
            function ($elementOne, $elementTwo) {
                return ($elementOne['sort_order'] < $elementTwo['sort_order']) ? -1 : 1;
            }
        );
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $allowedType = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type')->getAllowedType($storeManager->getStore()->getId());
        foreach ($types as $typeId => $type) {
        	if(!in_array($typeId,$allowedType))
        		continue;
            $splitButtonOptions[$typeId] = [
                'label' => __($type['label']),
                'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
                'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $typeId,
				'href' =>$this->_getProductCreateUrl($typeId)
            ];
        }

        return $splitButtonOptions;
    }
   
    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
   
        return $this->getUrl(
            'csproduct/*/new',
            ['set' => $this->_productFactory->create()->getDefaultAttributeSetId(), 'type' => $type]
        );
    }
    protected function _getAddButtonOptions()
    {
    
    	$splitButtonOptions[] = [
    	'label' => __('Add New'),
    	'onclick' => "setLocation('" . $this->_getCreateUrl() . "')",
    	'area' => 'adminhtml'
    			];
    
    	return $splitButtonOptions;
    } 
	
    protected function _getCreateUrl()
    {
    	return $this->getUrl(
    			'*/*/new'
    	);
    }
   	
    public function getGridHtml()
    {
    	return $this->getChildHtml('grid');
    }
    
    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
    	return $this->_storeManager->isSingleStoreMode();
    }
}
