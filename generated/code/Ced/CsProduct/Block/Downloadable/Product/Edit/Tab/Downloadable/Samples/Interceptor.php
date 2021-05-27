<?php
namespace Ced\CsProduct\Block\Downloadable\Product\Edit\Tab\Downloadable\Samples;

/**
 * Interceptor class for @see \Ced\CsProduct\Block\Downloadable\Product\Edit\Tab\Downloadable\Samples
 */
class Interceptor extends \Ced\CsProduct\Block\Downloadable\Product\Edit\Tab\Downloadable\Samples implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase, \Magento\Downloadable\Helper\File $downloadableFile, \Magento\Framework\Registry $coreRegistry, \Magento\Downloadable\Model\Sample $sampleModel, \Magento\Backend\Model\UrlFactory $urlFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $coreFileStorageDatabase, $downloadableFile, $coreRegistry, $sampleModel, $urlFactory, $data);
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
