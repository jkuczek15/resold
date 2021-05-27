<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Duo\Authpost;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Duo\Authpost
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Duo\Authpost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $session, \Magento\Framework\View\Result\PageFactory $pageFactory, \MSP\TwoFactorAuth\Model\Provider\Engine\DuoSecurity $duoSecurity, \MSP\TwoFactorAuth\Api\TfaSessionInterface $tfaSession, \Magento\Framework\DataObjectFactory $dataObjectFactory, \MSP\TwoFactorAuth\Model\AlertInterface $alert, \MSP\TwoFactorAuth\Api\TfaInterface $tfa)
    {
        $this->___init();
        parent::__construct($context, $session, $pageFactory, $duoSecurity, $tfaSession, $dataObjectFactory, $alert, $tfa);
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
