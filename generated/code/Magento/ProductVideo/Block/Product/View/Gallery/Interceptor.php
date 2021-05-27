<?php
namespace Magento\ProductVideo\Block\Product\View\Gallery;

/**
 * Interceptor class for @see \Magento\ProductVideo\Block\Product\View\Gallery
 */
class Interceptor extends \Magento\ProductVideo\Block\Product\View\Gallery implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Stdlib\ArrayUtils $arrayUtils, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\ProductVideo\Helper\Media $mediaHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $arrayUtils, $jsonEncoder, $mediaHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsMediaGalleryDataJson()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOptionsMediaGalleryDataJson');
        if (!$pluginInfo) {
            return parent::getOptionsMediaGalleryDataJson();
        } else {
            return $this->___callPlugins('getOptionsMediaGalleryDataJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        if (!$pluginInfo) {
            return parent::getImage($product, $imageId, $attributes);
        } else {
            return $this->___callPlugins('getImage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}
