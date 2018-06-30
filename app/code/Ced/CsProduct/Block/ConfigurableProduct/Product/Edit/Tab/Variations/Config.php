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

namespace Ced\CsProduct\Block\ConfigurableProduct\Product\Edit\Tab\Variations;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
class Config extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config
{
    protected $_template = 'configurable/product/edit/super/config.phtml';

/**
     * @var string
     */

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Configurable $configurableType
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Configurable $configurableType,
        array $data = []
    ) {
    	
        $this->_coreRegistry = $coreRegistry;
        $this->configurableType = $configurableType;
        parent::__construct($context, $coreRegistry,$configurableType,$data);
    }

    /**
     * Initialize block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setProductId($this->getRequest()->getParam('id'));

        $this->setId('config_super_product');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Retrieve Tab class (for loading)
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Configurations');
    }

    /**
     * Retrieve Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Configurations');
    }

    /**
     * Can show tab flag
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check is a hidden tab
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get parent tab code
     *
     * @return string
     */
    public function getParentTab()
    {
        return 'product-details';
    }

    /**
     * @return bool
     */
    public function isHasVariations()
    {
        return $this->getProduct()->getTypeId() === Configurable::TYPE_CODE
            && $this->configurableType->getUsedProducts($this->getProduct());
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setData('opened', $this->getProduct()->getTypeId() === Configurable::TYPE_CODE);
        return parent::_prepareLayout();
    }
}
