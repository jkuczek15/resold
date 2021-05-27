<?php
namespace Ced\CsMarketplace\Controller\Account\ApprovalPost;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Account\ApprovalPost
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Account\ApprovalPost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Customer\Model\CustomerExtractor $customerExtractor, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Ced\CsMarketplace\Model\Vendor $Vendor)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $customerAccountManagement, $customerRepository, $formKeyValidator, $customerExtractor, $urlFactory, $moduleManager, $Vendor);
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
