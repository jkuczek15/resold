<?php
namespace Magento\Paypal\Block\Adminhtml\System\Config\Field\Country;

/**
 * Interceptor class for @see \Magento\Paypal\Block\Adminhtml\System\Config\Field\Country
 */
class Interceptor extends \Magento\Paypal\Block\Adminhtml\System\Config\Field\Country implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Model\Url $url, \Magento\Framework\View\Helper\Js $jsHelper, \Magento\Directory\Helper\Data $directoryHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $url, $jsHelper, $directoryHelper, $data);
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
