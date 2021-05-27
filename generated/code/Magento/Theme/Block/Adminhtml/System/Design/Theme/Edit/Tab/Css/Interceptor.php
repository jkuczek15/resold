<?php
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css;

/**
 * Interceptor class for @see \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css
 */
class Interceptor extends \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Theme\Model\Uploader\Service $uploaderService, \Magento\Framework\Encryption\UrlCoder $urlCoder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $objectManager, $uploaderService, $urlCoder, $data);
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
