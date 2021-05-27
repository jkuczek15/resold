<?php
namespace Mageplaza\DeleteOrders\Controller\Adminhtml\Order\MassDelete;

/**
 * Interceptor class for @see \Mageplaza\DeleteOrders\Controller\Adminhtml\Order\MassDelete
 */
class Interceptor extends \Mageplaza\DeleteOrders\Controller\Adminhtml\Order\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory, \Magento\Sales\Model\OrderRepository $orderRepository, \Mageplaza\DeleteOrders\Helper\Data $dataHelper, \Psr\Log\LoggerInterface $logger, \Magento\Sales\Api\OrderManagementInterface $orderManagement)
    {
        $this->___init();
        parent::__construct($context, $filter, $collectionFactory, $orderRepository, $dataHelper, $logger, $orderManagement);
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
