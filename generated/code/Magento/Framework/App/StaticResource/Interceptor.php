<?php
namespace Magento\Framework\App\StaticResource;

/**
 * Interceptor class for @see \Magento\Framework\App\StaticResource
 */
class Interceptor extends \Magento\Framework\App\StaticResource implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\State $state, \Magento\Framework\App\Response\FileInterface $response, \Magento\Framework\App\Request\Http $request, \Magento\Framework\App\View\Asset\Publisher $publisher, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\Module\ModuleList $moduleList, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader)
    {
        $this->___init();
        parent::__construct($state, $response, $request, $publisher, $assetRepo, $moduleList, $objectManager, $configLoader);
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
