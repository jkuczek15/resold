<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Google\Qr;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Google\Qr
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Google\Qr implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $session, \Magento\Framework\View\Result\PageFactory $pageFactory, \MSP\TwoFactorAuth\Api\TfaInterface $tfa, \MSP\TwoFactorAuth\Model\Provider\Engine\Google $google, \Magento\Framework\Controller\Result\Raw $rawResult)
    {
        $this->___init();
        parent::__construct($context, $session, $pageFactory, $tfa, $google, $rawResult);
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
