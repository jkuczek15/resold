<?php
namespace MageVision\MassEmailCustomers\Controller\Adminhtml\Email\MassSend;

/**
 * Interceptor class for @see \MageVision\MassEmailCustomers\Controller\Adminhtml\Email\MassSend
 */
class Interceptor extends \MageVision\MassEmailCustomers\Controller\Adminhtml\Email\MassSend implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \MageVision\MassEmailCustomers\Helper\Data $helper, \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesCollectionFactory)
    {
        $this->___init();
        parent::__construct($context, $filter, $helper, $customerCollectionFactory, $salesCollectionFactory);
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
