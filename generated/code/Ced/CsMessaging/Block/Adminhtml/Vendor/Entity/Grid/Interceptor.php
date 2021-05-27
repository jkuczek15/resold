<?php
namespace Ced\CsMessaging\Block\Adminhtml\Vendor\Entity\Grid;

/**
 * Interceptor class for @see \Ced\CsMessaging\Block\Adminhtml\Vendor\Entity\Grid
 */
class Interceptor extends \Ced\CsMessaging\Block\Adminhtml\Vendor\Entity\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Ced\CsMarketplace\Model\VendorFactory $vendorFactory, \Magento\Store\Model\WebsiteFactory $websiteFactory, \Magento\Framework\Module\Manager $moduleManager, \Ced\CsMarketplace\Model\System\Config\Source\Group $group, \Ced\CsMarketplace\Model\System\Config\Source\Status $status, \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $vendorFactory, $websiteFactory, $moduleManager, $group, $status, $pageLayoutBuilder, $data);
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
