<?php
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vorders;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vorders
 */
class Interceptor extends \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vorders implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Ced\CsMarketplace\Model\VordersFactory $vordersFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder, \Magento\Sales\Model\Order\Invoice $invoice, \Ced\CsMarketplace\Helper\Data $helperData, \Magento\Framework\App\ResourceConnection $resource, \Ced\CsMarketplace\Model\Vorders $vorders, \Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $vordersFactory, $moduleManager, $pageLayoutBuilder, $invoice, $helperData, $resource, $vorders, $objectManager, $data);
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
