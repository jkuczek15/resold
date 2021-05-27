<?php
namespace Ced\CsVendorReview\Block\Adminhtml\Rating\Grid;

/**
 * Interceptor class for @see \Ced\CsVendorReview\Block\Adminhtml\Rating\Grid
 */
class Interceptor extends \Ced\CsVendorReview\Block\Adminhtml\Rating\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Store\Model\WebsiteFactory $websiteFactory, \Ced\CsVendorReview\Model\ResourceModel\Rating\Collection $collectionFactory, \Magento\Framework\Module\Manager $moduleManager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $messageManager, $websiteFactory, $collectionFactory, $moduleManager, $data);
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
