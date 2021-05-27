<?php
namespace Magento\MediaStorage\Model\File\Storage\Directory\Database;

/**
 * Interceptor class for @see \Magento\MediaStorage\Model\File\Storage\Directory\Database
 */
class Interceptor extends \Magento\MediaStorage\Model\File\Storage\Directory\Database implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb, \Magento\Framework\Stdlib\DateTime\DateTime $dateModel, \Magento\Framework\App\Config\ScopeConfigInterface $configuration, \Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory $directoryFactory, \Magento\MediaStorage\Model\ResourceModel\File\Storage\Directory\Database $resource, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, $connectionName = null, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $coreFileStorageDb, $dateModel, $configuration, $directoryFactory, $resource, $resourceCollection, $connectionName, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function createRecursive($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createRecursive');
        if (!$pluginInfo) {
            return parent::createRecursive($path);
        } else {
            return $this->___callPlugins('createRecursive', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubdirectories($directory)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSubdirectories');
        if (!$pluginInfo) {
            return parent::getSubdirectories($directory);
        } else {
            return $this->___callPlugins('getSubdirectories', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($dirPath)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'deleteDirectory');
        if (!$pluginInfo) {
            return parent::deleteDirectory($dirPath);
        } else {
            return $this->___callPlugins('deleteDirectory', func_get_args(), $pluginInfo);
        }
    }
}
