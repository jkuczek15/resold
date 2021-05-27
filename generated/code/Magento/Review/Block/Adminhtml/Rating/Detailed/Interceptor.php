<?php
namespace Magento\Review\Block\Adminhtml\Rating\Detailed;

/**
 * Interceptor class for @see \Magento\Review\Block\Adminhtml\Rating\Detailed
 */
class Interceptor extends \Magento\Review\Block\Adminhtml\Rating\Detailed implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Review\Model\ResourceModel\Rating\CollectionFactory $ratingsFactory, \Magento\Review\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $votesFactory, \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $ratingsFactory, $votesFactory, $registry, $data);
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
