<?php
namespace Ced\CsProduct\Controller\Configurable\Attribute\CreateOptions;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Configurable\Attribute\CreateOptions
 */
class Interceptor extends \Ced\CsProduct\Controller\Configurable\Attribute\CreateOptions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory)
    {
        $this->___init();
        parent::__construct($context, $jsonHelper, $attributeFactory);
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
