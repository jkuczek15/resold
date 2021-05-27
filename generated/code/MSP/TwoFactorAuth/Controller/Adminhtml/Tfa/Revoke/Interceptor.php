<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Tfa\Revoke;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Tfa\Revoke
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Tfa\Revoke implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \MSP\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager)
    {
        $this->___init();
        parent::__construct($context, $trustedManager);
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
