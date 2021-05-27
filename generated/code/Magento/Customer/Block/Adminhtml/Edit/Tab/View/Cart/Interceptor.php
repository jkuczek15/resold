<?php
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View\Cart;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Edit\Tab\View\Cart
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Edit\Tab\View\Cart implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Framework\Data\CollectionFactory $dataCollectionFactory, \Magento\Framework\Registry $coreRegistry, \Magento\Quote\Model\QuoteFactory $quoteFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $quoteRepository, $dataCollectionFactory, $coreRegistry, $quoteFactory, $data);
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
