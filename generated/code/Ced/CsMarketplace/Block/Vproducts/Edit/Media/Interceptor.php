<?php
namespace Ced\CsMarketplace\Block\Vproducts\Edit\Media;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Vproducts\Edit\Media
 */
class Interceptor extends \Ced\CsMarketplace\Block\Vproducts\Edit\Media implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type $type)
    {
        $this->___init();
        parent::__construct($context, $objectManager, $type);
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
