<?php
namespace MSP\TwoFactorAuth\Controller\Adminhtml\Tfa\Reset;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Controller\Adminhtml\Tfa\Reset
 */
class Interceptor extends \MSP\TwoFactorAuth\Controller\Adminhtml\Tfa\Reset implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\User\Model\ResourceModel\User $userResourceModel, \MSP\TwoFactorAuth\Api\TfaInterface $tfa, \Magento\User\Model\UserFactory $userFactory)
    {
        $this->___init();
        parent::__construct($context, $userResourceModel, $tfa, $userFactory);
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
