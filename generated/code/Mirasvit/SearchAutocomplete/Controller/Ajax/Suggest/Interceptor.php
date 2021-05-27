<?php
namespace Mirasvit\SearchAutocomplete\Controller\Ajax\Suggest;

/**
 * Interceptor class for @see \Mirasvit\SearchAutocomplete\Controller\Ajax\Suggest
 */
class Interceptor extends \Mirasvit\SearchAutocomplete\Controller\Ajax\Suggest implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\SearchAutocomplete\Model\Result $result, \Magento\Framework\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($result, $context);
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
