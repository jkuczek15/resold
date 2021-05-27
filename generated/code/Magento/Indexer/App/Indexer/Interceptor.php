<?php
namespace Magento\Indexer\App\Indexer;

/**
 * Interceptor class for @see \Magento\Indexer\App\Indexer
 */
class Interceptor extends \Magento\Indexer\App\Indexer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($reportDir, \Magento\Framework\Filesystem $filesystem, \Magento\Indexer\Model\Processor $processor, \Magento\Framework\App\Console\Response $response)
    {
        $this->___init();
        parent::__construct($reportDir, $filesystem, $processor, $response);
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
