<?php
namespace Magento\Shipping\Block\Adminhtml\Order\Packaging\Grid;

/**
 * Interceptor class for @see \Magento\Shipping\Block\Adminhtml\Order\Packaging\Grid
 */
class Interceptor extends \Magento\Shipping\Block\Adminhtml\Order\Packaging\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory, \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $shipmentItemFactory, $registry, $data);
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
