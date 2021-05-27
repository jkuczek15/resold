<?php
namespace Ced\CsProduct\Controller\Wysiwyg\Directive;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Wysiwyg\Directive
 */
class Interceptor extends \Ced\CsProduct\Controller\Wysiwyg\Directive implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Url\DecoderInterface $urlDecoder, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory)
    {
        $this->___init();
        parent::__construct($context, $urlDecoder, $resultRawFactory);
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
