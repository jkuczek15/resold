<?php
namespace Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vpayments;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vpayments
 */
class Interceptor extends \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Edit\Tab\Vpayments implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Ced\CsMarketplace\Model\VpaymentFactory $vpaymentFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder, \Magento\Framework\ObjectManagerInterface $objectmanager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $vpaymentFactory, $moduleManager, $coreRegistry, $pageLayoutBuilder, $objectmanager, $data);
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
