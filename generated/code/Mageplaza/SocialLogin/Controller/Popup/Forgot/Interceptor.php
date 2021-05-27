<?php
namespace Mageplaza\SocialLogin\Controller\Popup\Forgot;

/**
 * Interceptor class for @see \Mageplaza\SocialLogin\Controller\Popup\Forgot
 */
class Interceptor extends \Mageplaza\SocialLogin\Controller\Popup\Forgot implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement, \Magento\Framework\Escaper $escaper, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Captcha\Helper\Data $captchaHelper, \Mageplaza\SocialLogin\Helper\Data $socialHelper)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $customerAccountManagement, $escaper, $resultJsonFactory, $captchaHelper, $socialHelper);
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
