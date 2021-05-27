<?php
namespace Ced\FbNative\Block\Adminhtml\Form\Field\MagentoAttributes;

/**
 * Interceptor class for @see \Ced\FbNative\Block\Adminhtml\Form\Field\MagentoAttributes
 */
class Interceptor extends \Ced\FbNative\Block\Adminhtml\Form\Field\MagentoAttributes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Ced\FbNative\Model\Source\FbAttribute\FbAttributes $fbAttr, \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $searchCriteriaBuilder, $fbAttr, $collection, $data);
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
