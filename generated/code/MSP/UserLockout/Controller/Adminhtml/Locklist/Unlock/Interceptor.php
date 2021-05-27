<?php
namespace MSP\UserLockout\Controller\Adminhtml\Locklist\Unlock;

/**
 * Interceptor class for @see \MSP\UserLockout\Controller\Adminhtml\Locklist\Unlock
 */
class Interceptor extends \MSP\UserLockout\Controller\Adminhtml\Locklist\Unlock implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \MSP\UserLockout\Api\LockoutInterface $lockout, \MSP\UserLockout\Model\ResourceModel\Lockout\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $filter, $lockout, $collectionFactory);
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
