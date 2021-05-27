<?php
namespace Magento\Framework\View\Asset\Config;

/**
 * Interceptor class for @see \Magento\Framework\View\Asset\Config
 */
class Interceptor extends \Magento\Framework\View\Asset\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($scopeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function isMergeCssFiles()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMergeCssFiles');
        if (!$pluginInfo) {
            return parent::isMergeCssFiles();
        } else {
            return $this->___callPlugins('isMergeCssFiles', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isBundlingJsFiles()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isBundlingJsFiles');
        if (!$pluginInfo) {
            return parent::isBundlingJsFiles();
        } else {
            return $this->___callPlugins('isBundlingJsFiles', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMergeJsFiles()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMergeJsFiles');
        if (!$pluginInfo) {
            return parent::isMergeJsFiles();
        } else {
            return $this->___callPlugins('isMergeJsFiles', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isMinifyHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isMinifyHtml');
        if (!$pluginInfo) {
            return parent::isMinifyHtml();
        } else {
            return $this->___callPlugins('isMinifyHtml', func_get_args(), $pluginInfo);
        }
    }
}
