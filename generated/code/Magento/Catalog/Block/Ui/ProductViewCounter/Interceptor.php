<?php
namespace Magento\Catalog\Block\Ui\ProductViewCounter;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Ui\ProductViewCounter
 */
class Interceptor extends \Magento\Catalog\Block\Ui\ProductViewCounter implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorComposite $productRenderCollectorComposite, \Magento\Store\Model\StoreManager $storeManager, \Magento\Catalog\Model\ProductRenderFactory $productRenderFactory, \Magento\Framework\EntityManager\Hydrator $hydrator, \Magento\Framework\Serialize\SerializerInterface $serialize, \Magento\Framework\Url $url, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $productRepository, $productRenderCollectorComposite, $storeManager, $productRenderFactory, $hydrator, $serialize, $url, $registry);
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
