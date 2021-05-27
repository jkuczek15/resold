<?php
namespace Magento\Sales\Block\Adminhtml\Order\Totals\Tax;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\Totals\Tax
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\Totals\Tax implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Tax\Model\Config $taxConfig, \Magento\Tax\Helper\Data $taxHelper, \Magento\Tax\Model\Calculation $taxCalculation, \Magento\Tax\Model\Sales\Order\TaxFactory $taxOrderFactory, \Magento\Sales\Helper\Admin $salesAdminHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $taxConfig, $taxHelper, $taxCalculation, $taxOrderFactory, $salesAdminHelper, $data);
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
