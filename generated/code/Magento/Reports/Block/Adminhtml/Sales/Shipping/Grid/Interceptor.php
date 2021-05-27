<?php
namespace Magento\Reports\Block\Adminhtml\Sales\Shipping\Grid;

/**
 * Interceptor class for @see \Magento\Reports\Block\Adminhtml\Sales\Shipping\Grid
 */
class Interceptor extends \Magento\Reports\Block\Adminhtml\Sales\Shipping\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory, \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory, \Magento\Reports\Helper\Data $reportsData, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $resourceFactory, $collectionFactory, $reportsData, $data);
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
