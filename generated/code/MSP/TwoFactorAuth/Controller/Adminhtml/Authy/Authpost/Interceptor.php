<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Authpost;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Authpost
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Authy\Authpost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $session, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \MSP\TwoFactorAuth\Model\Provider\Engine\Authy $authy, \MSP\TwoFactorAuth\Api\TfaSessionInterface $tfaSession, \MSP\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager, \MSP\TwoFactorAuth\Api\TfaInterface $tfa, \MSP\TwoFactorAuth\Model\AlertInterface $alert, \Magento\Framework\DataObjectFactory $dataObjectFactory)
    {
        $this->___init();
        parent::__construct($context, $session, $jsonFactory, $authy, $tfaSession, $trustedManager, $tfa, $alert, $dataObjectFactory);
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
