<?php
namespace Ced\CsMarketplace\Controller\Account\LoginPost;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Account\LoginPost
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Account\LoginPost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\AccountManagementInterface $vendorAccountManagement, \Ced\CsMarketplace\Model\Url $vendorHelperData, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Ced\CsMarketplace\Model\Account\Redirect $vendorAcRedirect)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $vendorAccountManagement, $vendorHelperData, $formKeyValidator, $vendorAcRedirect);
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
