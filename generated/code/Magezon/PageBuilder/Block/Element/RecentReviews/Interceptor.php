<?php
namespace Magezon\PageBuilder\Block\Element\RecentReviews;

/**
 * Interceptor class for @see \Magezon\PageBuilder\Block\Element\RecentReviews
 */
class Interceptor extends \Magezon\PageBuilder\Block\Element\RecentReviews implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\App\Http\Context $httpContext, \Magezon\Core\Helper\Data $coreHelper, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $httpContext, $coreHelper, $priceCurrency, $collectionFactory, $productCollectionFactory, $data);
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
