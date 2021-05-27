<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Configureverifypost;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Configureverifypost
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Configureverifypost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $session, \MSP\TwoFactorAuth\Api\TfaInterface $tfa, \MSP\TwoFactorAuth\Api\TfaSessionInterface $tfaSession, \MSP\TwoFactorAuth\Model\AlertInterface $alert, \MSP\TwoFactorAuth\Model\Provider\Engine\Authy $authy, \MSP\TwoFactorAuth\Model\Provider\Engine\Authy\Verification $verification, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory)
    {
        $this->___init();
        parent::__construct($context, $session, $tfa, $tfaSession, $alert, $authy, $verification, $jsonFactory);
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
