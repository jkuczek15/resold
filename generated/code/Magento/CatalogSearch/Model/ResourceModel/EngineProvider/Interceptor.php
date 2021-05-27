<?php
namespace Magento\CatalogSearch\Model\ResourceModel\EngineProvider;

/**
 * Interceptor class for @see \Magento\CatalogSearch\Model\ResourceModel\EngineProvider
 */
class Interceptor extends \Magento\CatalogSearch\Model\ResourceModel\EngineProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\ObjectManagerInterface $objectManager, array $engines)
    {
        $this->___init();
        parent::__construct($scopeConfig, $objectManager, $engines);
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'get');
        if (!$pluginInfo) {
            return parent::get();
        } else {
            return $this->___callPlugins('get', func_get_args(), $pluginInfo);
        }
    }
}
