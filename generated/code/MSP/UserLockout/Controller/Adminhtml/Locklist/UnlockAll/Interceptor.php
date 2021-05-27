<?php
namespace MSP\UserLockout\Controller\Adminhtml\Locklist\UnlockAll;

/**
 * Interceptor class for @see \MSP\UserLockout\Controller\Adminhtml\Locklist\UnlockAll
 */
class Interceptor extends \MSP\UserLockout\Controller\Adminhtml\Locklist\UnlockAll implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MSP\UserLockout\Api\LockoutInterface $lockout)
    {
        $this->___init();
        parent::__construct($context, $lockout);
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
