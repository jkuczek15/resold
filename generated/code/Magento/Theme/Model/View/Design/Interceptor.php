<?php
namespace Magento\Theme\Model\View\Design;

/**
 * Interceptor class for @see \Magento\Theme\Model\View\Design
 */
class Interceptor extends \Magento\Theme\Model\View\Design implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\View\Design\Theme\FlyweightFactory $flyweightFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Theme\Model\ThemeFactory $themeFactory, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\App\State $appState, array $themes)
    {
        $this->___init();
        parent::__construct($storeManager, $flyweightFactory, $scopeConfig, $themeFactory, $objectManager, $appState, $themes);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationDesignTheme($area = null, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getConfigurationDesignTheme');
        if (!$pluginInfo) {
            return parent::getConfigurationDesignTheme($area, $params);
        } else {
            return $this->___callPlugins('getConfigurationDesignTheme', func_get_args(), $pluginInfo);
        }
    }
}
