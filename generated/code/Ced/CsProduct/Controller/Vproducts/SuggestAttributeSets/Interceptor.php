<?php
namespace Ced\CsProduct\Controller\Vproducts\SuggestAttributeSets;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Vproducts\SuggestAttributeSets
 */
class Interceptor extends \Ced\CsProduct\Controller\Vproducts\SuggestAttributeSets implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Catalog\Model\Product\AttributeSet\SuggestedSet $suggestedSet)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $suggestedSet);
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
