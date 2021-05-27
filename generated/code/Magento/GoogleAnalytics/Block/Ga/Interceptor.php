<?php
namespace Magento\GoogleAnalytics\Block\Ga;

/**
 * Interceptor class for @see \Magento\GoogleAnalytics\Block\Ga
 */
class Interceptor extends \Magento\GoogleAnalytics\Block\Ga implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection, \Magento\GoogleAnalytics\Helper\Data $googleAnalyticsData, array $data = [], \Magento\Cookie\Helper\Cookie $cookieHelper = null)
    {
        $this->___init();
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $data, $cookieHelper);
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
