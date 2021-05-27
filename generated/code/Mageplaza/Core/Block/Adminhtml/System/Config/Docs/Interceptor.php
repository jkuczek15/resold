<?php
namespace Mageplaza\Core\Block\Adminhtml\System\Config\Docs;

/**
 * Interceptor class for @see \Mageplaza\Core\Block\Adminhtml\System\Config\Docs
 */
class Interceptor extends \Mageplaza\Core\Block\Adminhtml\System\Config\Docs implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Mageplaza\Core\Helper\Validate $helper, \Magento\Framework\Module\PackageInfoFactory $packageInfoFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helper, $packageInfoFactory, $data);
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
