<?php
namespace Mageplaza\Smtp\Controller\Adminhtml\Smtp\Clear;

/**
 * Interceptor class for @see \Mageplaza\Smtp\Controller\Adminhtml\Smtp\Clear
 */
class Interceptor extends \Mageplaza\Smtp\Controller\Adminhtml\Smtp\Clear implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Mageplaza\Smtp\Model\ResourceModel\Log\CollectionFactory $collectionLog)
    {
        $this->___init();
        parent::__construct($context, $collectionLog);
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
