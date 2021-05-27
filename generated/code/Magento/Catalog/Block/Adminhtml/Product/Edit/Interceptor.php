<?php
namespace Magento\Catalog\Block\Adminhtml\Product\Edit;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Adminhtml\Product\Edit
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Product\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory, \Magento\Framework\Registry $registry, \Magento\Catalog\Helper\Product $productHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $attributeSetFactory, $registry, $productHelper, $data);
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
