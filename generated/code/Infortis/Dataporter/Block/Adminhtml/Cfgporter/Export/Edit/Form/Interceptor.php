<?php
namespace Infortis\Dataporter\Block\Adminhtml\Cfgporter\Export\Edit\Form;

/**
 * Interceptor class for @see \Infortis\Dataporter\Block\Adminhtml\Cfgporter\Export\Edit\Form
 */
class Interceptor extends \Infortis\Dataporter\Block\Adminhtml\Cfgporter\Export\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, array $data, \Magento\Store\Model\System\Store $systemStore, \Magento\Framework\View\LayoutFactory $viewLayoutFactory)
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $data, $systemStore, $viewLayoutFactory);
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
