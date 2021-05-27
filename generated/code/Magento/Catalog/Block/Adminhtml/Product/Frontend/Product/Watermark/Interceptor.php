<?php
namespace Magento\Catalog\Block\Adminhtml\Product\Frontend\Product\Watermark;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Adminhtml\Product\Frontend\Product\Watermark
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Product\Frontend\Product\Watermark implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Catalog\Model\Config\Source\Watermark\Position $watermarkPosition, \Magento\Config\Block\System\Config\Form\Field $formField, \Magento\Framework\Data\Form\Element\Factory $elementFactory, array $imageTypes = [], array $data = [])
    {
        $this->___init();
        parent::__construct($context, $watermarkPosition, $formField, $elementFactory, $imageTypes, $data);
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
