<?php
namespace Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content\Uploader;

/**
 * Interceptor class for @see \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content\Uploader
 */
class Interceptor extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content\Uploader implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\File\Size $fileSize, \Magento\Cms\Model\Wysiwyg\Images\Storage $imagesStorage, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $fileSize, $imagesStorage, $data);
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
