<?php
namespace Magento\Framework\App\Cron;

/**
 * Interceptor class for @see \Magento\Framework\App\Cron
 */
class Interceptor extends \Magento\Framework\App\Cron implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\State $state, \Magento\Framework\App\Console\Request $request, \Magento\Framework\App\Console\Response $response, \Magento\Framework\ObjectManagerInterface $objectManager, array $parameters = [], \Magento\Framework\App\AreaList $areaList = null)
    {
        $this->___init();
        parent::__construct($state, $request, $response, $objectManager, $parameters, $areaList);
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
