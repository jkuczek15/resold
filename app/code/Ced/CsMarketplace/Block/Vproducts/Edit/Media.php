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
 
namespace Ced\CsMarketplace\Block\Vproducts\Edit;

class Media extends \Magento\Framework\View\Element\Template
{
	protected $_objectManager;
	protected $_type;
	protected $_registry;
	
	/**
	 * Get set collection of products
	 *
	 */
	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type $type
	) {
		
		parent::__construct($context);
		$this->_context = $context;	
		$this->_objectManager = $objectManager;
		$this->_type = $type;
		$this->_registry = $this->_objectManager->get('Magento\Framework\Registry');;
		
	}
	public function getRegistry(){
		return $this->_registry;
	}
	public function getProductImageHelper(){
		return $this->_objectManager->get('Magento\Catalog\Helper\Image');
	}
	
	
	
}
