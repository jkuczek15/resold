<?php
namespace Magento\Backend\Block\Dashboard\Tab\Orders;

/**
 * Interceptor class for @see \Magento\Backend\Block\Dashboard\Tab\Orders
 */
class Interceptor extends \Magento\Backend\Block\Dashboard\Tab\Orders implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Reports\Model\ResourceModel\Order\CollectionFactory $collectionFactory, \Magento\Backend\Helper\Dashboard\Data $dashboardData, \Magento\Backend\Helper\Dashboard\Order $dataHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $collectionFactory, $dashboardData, $dataHelper, $data);
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
