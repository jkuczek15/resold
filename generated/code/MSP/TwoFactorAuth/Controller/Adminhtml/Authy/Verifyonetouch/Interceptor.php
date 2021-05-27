<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Verifyonetouch;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Verifyonetouch
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Verifyonetouch implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \MSP\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager, \MSP\TwoFactorAuth\Api\TfaSessionInterface $tfaSession, \MSP\TwoFactorAuth\Api\TfaInterface $tfa, \MSP\TwoFactorAuth\Model\AlertInterface $alert, \MSP\TwoFactorAuth\Model\Provider\Engine\Authy\OneTouch $oneTouch, \Magento\Backend\Model\Auth\Session $session)
    {
        $this->___init();
        parent::__construct($context, $jsonFactory, $trustedManager, $tfaSession, $tfa, $alert, $oneTouch, $session);
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
