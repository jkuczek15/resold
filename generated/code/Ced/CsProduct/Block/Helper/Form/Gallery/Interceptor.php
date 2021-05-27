<?php
namespace Ced\CsProduct\Block\Helper\Form\Gallery;

/**
 * Interceptor class for @see \Ced\CsProduct\Block\Helper\Form\Gallery
 */
class Interceptor extends \Ced\CsProduct\Block\Helper\Form\Gallery implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Registry $registry, \Magento\Framework\Data\Form $form, $data = [])
    {
        $this->___init();
        parent::__construct($context, $storeManager, $registry, $form, $data);
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
