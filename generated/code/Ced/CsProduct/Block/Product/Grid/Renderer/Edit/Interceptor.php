<?php
namespace Ced\CsProduct\Block\Product\Grid\Renderer\Edit;

/**
 * Interceptor class for @see \Ced\CsProduct\Block\Product\Grid\Renderer\Edit
 */
class Interceptor extends \Ced\CsProduct\Block\Product\Grid\Renderer\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Ced\CsMarketplace\Model\Vproducts $vproduct, \Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $vproduct, $objectManager, $data);
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
