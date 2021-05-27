<?php
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Attributes;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Attributes
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Attributes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeAction, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $productFactory, $attributeAction, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}
