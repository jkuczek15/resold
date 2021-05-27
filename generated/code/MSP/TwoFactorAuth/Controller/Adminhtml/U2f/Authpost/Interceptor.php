<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\U2f\Authpost;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\U2f\Authpost
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\U2f\Authpost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\MSP\TwoFactorAuth\Model\Tfa $tfa, \Magento\Backend\Model\Auth\Session $session, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \MSP\TwoFactorAuth\Api\TfaSessionInterface $tfaSession, \MSP\TwoFactorAuth\Api\TrustedManagerInterface $trustedManager, \MSP\TwoFactorAuth\Model\Provider\Engine\U2fKey $u2fKey, \Magento\Framework\DataObjectFactory $dataObjectFactory, \MSP\TwoFactorAuth\Model\AlertInterface $alert, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($tfa, $session, $jsonFactory, $tfaSession, $trustedManager, $u2fKey, $dataObjectFactory, $alert, $context);
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
