<?php
namespace Magento\MediaStorage\Model\File\Storage\Synchronization;

/**
 * Interceptor class for @see \Magento\MediaStorage\Model\File\Storage\Synchronization
 */
class Interceptor extends \Magento\MediaStorage\Model\File\Storage\Synchronization implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\MediaStorage\Model\File\Storage\DatabaseFactory $storageFactory, \Magento\Framework\Filesystem\Directory\WriteInterface $directory)
    {
        $this->___init();
        parent::__construct($storageFactory, $directory);
    }

    /**
     * {@inheritdoc}
     */
    public function synchronize($relativeFileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'synchronize');
        if (!$pluginInfo) {
            return parent::synchronize($relativeFileName);
        } else {
            return $this->___callPlugins('synchronize', func_get_args(), $pluginInfo);
        }
    }
}
