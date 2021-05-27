<?php
namespace Magento\Catalog\Block\Adminhtml\Product\Options\Ajax;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Adminhtml\Product\Options\Ajax
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Product\Options\Ajax implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $productFactory, $registry, $data);
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
