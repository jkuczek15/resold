<?php
namespace Magento\RequireJs\Block\Html\Head\Config;

/**
 * Interceptor class for @see \Magento\RequireJs\Block\Html\Head\Config
 */
class Interceptor extends \Magento\RequireJs\Block\Html\Head\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Framework\RequireJs\Config $config, \Magento\RequireJs\Model\FileManager $fileManager, \Magento\Framework\View\Page\Config $pageConfig, \Magento\Framework\View\Asset\ConfigInterface $bundleConfig, \Magento\Framework\View\Asset\Minification $minification, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $config, $fileManager, $pageConfig, $bundleConfig, $minification, $data);
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
