<?php
namespace Ced\CsMarketplace\Block\Vendor\Dashboard\MostSoldProducts;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Vendor\Dashboard\MostSoldProducts
 */
class Interceptor extends \Ced\CsMarketplace\Block\Vendor\Dashboard\MostSoldProducts implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\UrlFactory $urlFactory, \Magento\Catalog\Model\Product $collectionFactory, \Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $objectManager, $urlFactory, $collectionFactory, $resourceConnection);
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
