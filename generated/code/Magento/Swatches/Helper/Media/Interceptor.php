<?php
namespace Magento\Swatches\Helper\Media;

/**
 * Interceptor class for @see \Magento\Swatches\Helper\Media
 */
class Interceptor extends \Magento\Swatches\Helper\Media implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\Product\Media\Config $mediaConfig, \Magento\Framework\Filesystem $filesystem, \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Image\Factory $imageFactory, \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection, \Magento\Framework\View\ConfigInterface $configInterface)
    {
        $this->___init();
        parent::__construct($mediaConfig, $filesystem, $fileStorageDb, $storeManager, $imageFactory, $themeCollection, $configInterface);
    }

    /**
     * {@inheritdoc}
     */
    public function moveImageFromTmp($file)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'moveImageFromTmp');
        if (!$pluginInfo) {
            return parent::moveImageFromTmp($file);
        } else {
            return $this->___callPlugins('moveImageFromTmp', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateSwatchVariations($imageUrl)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'generateSwatchVariations');
        if (!$pluginInfo) {
            return parent::generateSwatchVariations($imageUrl);
        } else {
            return $this->___callPlugins('generateSwatchVariations', func_get_args(), $pluginInfo);
        }
    }
}
