<?php
namespace Magento\Widget\Model\Template\FilterEmulate;

/**
 * Interceptor class for @see \Magento\Widget\Model\Template\FilterEmulate
 */
class Interceptor extends \Magento\Widget\Model\Template\FilterEmulate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Stdlib\StringUtils $string, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Escaper $escaper, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Variable\Model\VariableFactory $coreVariableFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\View\LayoutInterface $layout, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\App\State $appState, \Magento\Framework\UrlInterface $urlModel, \Pelago\Emogrifier $emogrifier, \Magento\Email\Model\Source\Variables $configVariables, \Magento\Widget\Model\ResourceModel\Widget $widgetResource, \Magento\Widget\Model\Widget $widget)
    {
        $this->___init();
        parent::__construct($string, $logger, $escaper, $assetRepo, $scopeConfig, $coreVariableFactory, $storeManager, $layout, $layoutFactory, $appState, $urlModel, $emogrifier, $configVariables, $widgetResource, $widget);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'filter');
        if (!$pluginInfo) {
            return parent::filter($value);
        } else {
            return $this->___callPlugins('filter', func_get_args(), $pluginInfo);
        }
    }
}
