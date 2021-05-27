<?php
namespace Magento\Framework\App\DeploymentConfig\Writer;

/**
 * Interceptor class for @see \Magento\Framework\App\DeploymentConfig\Writer
 */
class Interceptor extends \Magento\Framework\App\DeploymentConfig\Writer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\DeploymentConfig\Reader $reader, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Config\File\ConfigFilePool $configFilePool, \Magento\Framework\App\DeploymentConfig $deploymentConfig, \Magento\Framework\App\DeploymentConfig\Writer\FormatterInterface $formatter = null, \Magento\Framework\App\DeploymentConfig\CommentParser $commentParser = null)
    {
        $this->___init();
        parent::__construct($reader, $filesystem, $configFilePool, $deploymentConfig, $formatter, $commentParser);
    }

    /**
     * {@inheritdoc}
     */
    public function saveConfig(array $data, $override = false, $pool = null, array $comments = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveConfig');
        if (!$pluginInfo) {
            return parent::saveConfig($data, $override, $pool, $comments);
        } else {
            return $this->___callPlugins('saveConfig', func_get_args(), $pluginInfo);
        }
    }
}
