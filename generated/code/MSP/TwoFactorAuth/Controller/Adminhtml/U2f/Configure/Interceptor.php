<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\U2f\Configure;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\U2f\Configure
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\U2f\Configure implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\MSP\TwoFactorAuth\Model\Tfa $tfa, \Magento\Backend\Model\Auth\Session $session, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($tfa, $session, $context);
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
