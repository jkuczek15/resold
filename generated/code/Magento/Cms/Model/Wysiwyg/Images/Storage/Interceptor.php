<?php
namespace Magento\Cms\Model\Wysiwyg\Images\Storage;

/**
 * Interceptor class for @see \Magento\Cms\Model\Wysiwyg\Images\Storage
 */
class Interceptor extends \Magento\Cms\Model\Wysiwyg\Images\Storage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Model\Session $session, \Magento\Backend\Model\UrlInterface $backendUrl, \Magento\Cms\Helper\Wysiwyg\Images $cmsWysiwygImages, \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Image\AdapterFactory $imageFactory, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory $storageCollectionFactory, \Magento\MediaStorage\Model\File\Storage\FileFactory $storageFileFactory, \Magento\MediaStorage\Model\File\Storage\DatabaseFactory $storageDatabaseFactory, \Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory $directoryDatabaseFactory, \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, array $resizeParameters = [], array $extensions = [], array $dirs = [], array $data = [])
    {
        $this->___init();
        parent::__construct($session, $backendUrl, $cmsWysiwygImages, $coreFileStorageDb, $filesystem, $imageFactory, $assetRepo, $storageCollectionFactory, $storageFileFactory, $storageDatabaseFactory, $directoryDatabaseFactory, $uploaderFactory, $resizeParameters, $extensions, $dirs, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getDirsCollection($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDirsCollection');
        if (!$pluginInfo) {
            return parent::getDirsCollection($path);
        } else {
            return $this->___callPlugins('getDirsCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resizeFile($source, $keepRation = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resizeFile');
        if (!$pluginInfo) {
            return parent::resizeFile($source, $keepRation);
        } else {
            return $this->___callPlugins('resizeFile', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbsPath($filePath = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getThumbsPath');
        if (!$pluginInfo) {
            return parent::getThumbsPath($filePath);
        } else {
            return $this->___callPlugins('getThumbsPath', func_get_args(), $pluginInfo);
        }
    }
}
