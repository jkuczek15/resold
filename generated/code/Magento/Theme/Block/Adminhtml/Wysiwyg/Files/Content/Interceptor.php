<?php
namespace Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content;

/**
 * Interceptor class for @see \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content
 */
class Interceptor extends \Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Theme\Helper\Storage $storageHelper, \Magento\Framework\Json\Helper\Data $jsonHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $storageHelper, $jsonHelper, $data);
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
