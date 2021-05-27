<?php
namespace Magento\Backend\Block\Widget\Form\Element\Dependence;

/**
 * Interceptor class for @see \Magento\Backend\Block\Widget\Form\Element\Dependence
 */
class Interceptor extends \Magento\Backend\Block\Widget\Form\Element\Dependence implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $fieldFactory, $data);
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
