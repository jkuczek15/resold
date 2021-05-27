<?php
namespace Magezon\Core\Block\Adminhtml\Product\Widget\Conditions;

/**
 * Interceptor class for @see \Magezon\Core\Block\Adminhtml\Product\Widget\Conditions
 */
class Interceptor extends \Magezon\Core\Block\Adminhtml\Product\Widget\Conditions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Data\Form\Element\Factory $elementFactory, \Magento\Rule\Block\Conditions $conditions, \Magento\Rule\Block\ConditionsFactory $conditionsFactory, \Magento\CatalogWidget\Model\Rule $rule, \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $elementFactory, $conditions, $conditionsFactory, $rule, $registry, $data);
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
