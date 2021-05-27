<?php
namespace Ced\CsMessaging\Controller\Adminhtml\Vendor\Massmessage;

/**
 * Interceptor class for @see \Ced\CsMessaging\Controller\Adminhtml\Vendor\Massmessage
 */
class Interceptor extends \Ced\CsMessaging\Controller\Adminhtml\Vendor\Massmessage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collectionFactory, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $registry);
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
