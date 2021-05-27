<?php
namespace Ced\CsVendorReview\Controller\Adminhtml\Rating\MassDelete;

/**
 * Interceptor class for @see \Ced\CsVendorReview\Controller\Adminhtml\Rating\MassDelete
 */
class Interceptor extends \Ced\CsVendorReview\Controller\Adminhtml\Rating\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\ResourceConnection $resource, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $resource, $data);
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
