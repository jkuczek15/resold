<?php
namespace Magento\Backend\App\UserConfig;

/**
 * Interceptor class for @see \Magento\Backend\App\UserConfig
 */
class Interceptor extends \Magento\Backend\App\UserConfig implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Config\Model\Config\Factory $configFactory, \Magento\Framework\App\Console\Response $response, array $request)
    {
        $this->___init();
        parent::__construct($configFactory, $response, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function launch()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'launch');
        if (!$pluginInfo) {
            return parent::launch();
        } else {
            return $this->___callPlugins('launch', func_get_args(), $pluginInfo);
        }
    }
}
