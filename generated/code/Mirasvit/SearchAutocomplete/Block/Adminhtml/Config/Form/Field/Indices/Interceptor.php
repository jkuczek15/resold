<?php
namespace Mirasvit\SearchAutocomplete\Block\Adminhtml\Config\Form\Field\Indices;

/**
 * Interceptor class for @see \Mirasvit\SearchAutocomplete\Block\Adminhtml\Config\Form\Field\Indices
 */
class Interceptor extends \Mirasvit\SearchAutocomplete\Block\Adminhtml\Config\Form\Field\Indices implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\SearchAutocomplete\Api\Repository\IndexRepositoryInterface $indexService, \Magento\Backend\Block\Template\Context $context)
    {
        $this->___init();
        parent::__construct($indexService, $context);
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
