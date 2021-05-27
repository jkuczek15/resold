<?php
namespace Magento\MediaStorage\Helper\File\Storage\Database;

/**
 * Interceptor class for @see \Magento\MediaStorage\Helper\File\Storage\Database
 */
class Interceptor extends \Magento\MediaStorage\Helper\File\Storage\Database implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\MediaStorage\Model\File\Storage\DatabaseFactory $dbStorageFactory, \Magento\MediaStorage\Model\File\Storage\File $fileStorage, \Magento\Framework\Filesystem $filesystem)
    {
        $this->___init();
        parent::__construct($context, $dbStorageFactory, $fileStorage, $filesystem);
    }

    /**
     * {@inheritdoc}
     */
    public function checkDbUsage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkDbUsage');
        if (!$pluginInfo) {
            return parent::checkDbUsage();
        } else {
            return $this->___callPlugins('checkDbUsage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageDatabaseModel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStorageDatabaseModel');
        if (!$pluginInfo) {
            return parent::getStorageDatabaseModel();
        } else {
            return $this->___callPlugins('getStorageDatabaseModel', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveFileToFilesystem($filename)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveFileToFilesystem');
        if (!$pluginInfo) {
            return parent::saveFileToFilesystem($filename);
        } else {
            return $this->___callPlugins('saveFileToFilesystem', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaRelativePath($fullPath)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMediaRelativePath');
        if (!$pluginInfo) {
            return parent::getMediaRelativePath($fullPath);
        } else {
            return $this->___callPlugins('getMediaRelativePath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFolder($folderName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'deleteFolder');
        if (!$pluginInfo) {
            return parent::deleteFolder($folderName);
        } else {
            return $this->___callPlugins('deleteFolder', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveUploadedFile($result)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveUploadedFile');
        if (!$pluginInfo) {
            return parent::saveUploadedFile($result);
        } else {
            return $this->___callPlugins('saveUploadedFile', func_get_args(), $pluginInfo);
        }
    }
}
