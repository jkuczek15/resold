<?php
namespace Ced\CsProduct\Controller\Configurable\Attribute\GetAttributes;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Configurable\Attribute\GetAttributes
 */
class Interceptor extends \Ced\CsProduct\Controller\Configurable\Attribute\GetAttributes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\ConfigurableProduct\Model\AttributesListInterface $attributesList)
    {
        $this->___init();
        parent::__construct($context, $storeManager, $jsonHelper, $attributesList);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
