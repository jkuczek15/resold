<?php
namespace Ced\CsVendorReview\Controller\Adminhtml\Rating\Edit;

/**
 * Interceptor class for @see \Ced\CsVendorReview\Controller\Adminhtml\Rating\Edit
 */
class Interceptor extends \Ced\CsVendorReview\Controller\Adminhtml\Rating\Edit implements \Magento\Framework\Interception\InterceptorInterface
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
