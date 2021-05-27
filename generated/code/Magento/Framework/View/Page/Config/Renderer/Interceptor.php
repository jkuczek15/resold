<?php
namespace Magento\Framework\View\Page\Config\Renderer;

/**
 * Interceptor class for @see \Magento\Framework\View\Page\Config\Renderer
 */
class Interceptor extends \Magento\Framework\View\Page\Config\Renderer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Page\Config $pageConfig, \Magento\Framework\View\Asset\MergeService $assetMergeService, \Magento\Framework\UrlInterface $urlBuilder, \Magento\Framework\Escaper $escaper, \Magento\Framework\Stdlib\StringUtils $string, \Psr\Log\LoggerInterface $logger)
    {
        $this->___init();
        parent::__construct($pageConfig, $assetMergeService, $urlBuilder, $escaper, $string, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function renderHeadContent()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'renderHeadContent');
        if (!$pluginInfo) {
            return parent::renderHeadContent();
        } else {
            return $this->___callPlugins('renderHeadContent', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renderMetadata()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'renderMetadata');
        if (!$pluginInfo) {
            return parent::renderMetadata();
        } else {
            return $this->___callPlugins('renderMetadata', func_get_args(), $pluginInfo);
        }
    }
}
