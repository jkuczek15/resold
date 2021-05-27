<?php
namespace Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;

/**
 * Interceptor class for @see \Magento\Framework\Search\Adapter\Mysql\TemporaryStorage
 */
class Interceptor extends \Magento\Framework\Search\Adapter\Mysql\TemporaryStorage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\App\DeploymentConfig $config = null)
    {
        $this->___init();
        parent::__construct($resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function storeApiDocuments($documents)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'storeApiDocuments');
        if (!$pluginInfo) {
            return parent::storeApiDocuments($documents);
        } else {
            return $this->___callPlugins('storeApiDocuments', func_get_args(), $pluginInfo);
        }
    }
}
