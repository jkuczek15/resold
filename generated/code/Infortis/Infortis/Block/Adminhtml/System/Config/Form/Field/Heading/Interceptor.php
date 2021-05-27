<?php
namespace Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field\Heading;

/**
 * Interceptor class for @see \Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field\Heading
 */
class Interceptor extends \Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field\Heading implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $data);
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
