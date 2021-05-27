<?php
namespace Ced\CsOrder\Block\Order\View\Tab\Invoices;

/**
 * Interceptor class for @see \Ced\CsOrder\Block\Order\View\Tab\Invoices
 */
class Interceptor extends \Ced\CsOrder\Block\Order\View\Tab\Invoices implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Registry $coreRegistry, \Ced\CsOrder\Model\ResourceModel\Invoice\CollectionFactory $invoiceFactory, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Sales\Model\Order\Invoice $invoice, \Magento\Customer\Model\Session $customerSession, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $coreRegistry, $invoiceFactory, $objectManager, $invoice, $customerSession, $data);
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
