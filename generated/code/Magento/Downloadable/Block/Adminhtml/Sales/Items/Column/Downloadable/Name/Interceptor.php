<?php
namespace Magento\Downloadable\Block\Adminhtml\Sales\Items\Column\Downloadable\Name;

/**
 * Interceptor class for @see \Magento\Downloadable\Block\Adminhtml\Sales\Items\Column\Downloadable\Name
 */
class Interceptor extends \Magento\Downloadable\Block\Adminhtml\Sales\Items\Column\Downloadable\Name implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration, \Magento\Framework\Registry $registry, \Magento\Catalog\Model\Product\OptionFactory $optionFactory, \Magento\Downloadable\Model\Link\PurchasedFactory $purchasedFactory, \Magento\Downloadable\Model\ResourceModel\Link\Purchased\Item\CollectionFactory $itemsFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $purchasedFactory, $itemsFactory, $data);
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
