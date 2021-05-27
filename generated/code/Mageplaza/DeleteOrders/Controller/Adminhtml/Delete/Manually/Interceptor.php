<?php
namespace Mageplaza\DeleteOrders\Controller\Adminhtml\Delete\Manually;

/**
 * Interceptor class for @see \Mageplaza\DeleteOrders\Controller\Adminhtml\Delete\Manually
 */
class Interceptor extends \Mageplaza\DeleteOrders\Controller\Adminhtml\Delete\Manually implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Mageplaza\DeleteOrders\Helper\Data $helperData, \Magento\Sales\Model\OrderRepository $orderRepository, \Psr\Log\LoggerInterface $logger, \Magento\Sales\Api\OrderManagementInterface $orderManagement, \Mageplaza\DeleteOrders\Helper\Email $email, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $helperData, $orderRepository, $logger, $orderManagement, $email, $storeManager);
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
