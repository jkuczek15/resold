<?php
namespace Magezon\Builder\Block\Element\SingleImage;

/**
 * Interceptor class for @see \Magezon\Builder\Block\Element\SingleImage
 */
class Interceptor extends \Magezon\Builder\Block\Element\SingleImage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magezon\Core\Helper\Data $coreHelper, \Magezon\Builder\Helper\Data $builderHelper, \Magezon\Builder\Helper\Image $builderImageHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $coreHelper, $builderHelper, $builderImageHelper, $data);
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
