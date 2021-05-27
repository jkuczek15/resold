<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Duo\Configure;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Duo\Configure
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Duo\Configure implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $session, \MSP\TwoFactorAuth\Api\TfaInterface $tfa)
    {
        $this->___init();
        parent::__construct($context, $session, $tfa);
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
