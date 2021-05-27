<?php
namespace Infortis\Brands\Block\ListBlock;

/**
 * Interceptor class for @see \Infortis\Brands\Block\ListBlock
 */
class Interceptor extends \Infortis\Brands\Block\ListBlock implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Infortis\Brands\Helper\Data $helperData, \Magento\Eav\Model\Config $modelConfig, \Magento\Catalog\Model\Product\Url $productUrl, \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection, \Magento\Catalog\Model\Product\Visibility $productVisibility, \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus, \Magento\CatalogInventory\Api\StockManagementInterface $modelStock, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $helperData, $modelConfig, $productUrl, $productCollection, $productVisibility, $productStatus, $modelStock, $data);
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
