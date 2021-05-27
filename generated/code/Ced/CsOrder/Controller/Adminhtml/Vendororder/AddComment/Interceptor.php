<?php
namespace Ced\CsOrder\Controller\Adminhtml\Vendororder\AddComment;

/**
 * Interceptor class for @see \Ced\CsOrder\Controller\Adminhtml\Vendororder\AddComment
 */
class Interceptor extends \Ced\CsOrder\Controller\Adminhtml\Vendororder\AddComment implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($context);
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
