<?php
namespace Ced\CsMarketplace\Controller\Account\CheckAvailability;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Account\CheckAvailability
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Account\CheckAvailability implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Ced\CsMarketplace\Model\Vendor $vendor)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $customerRepository, $dataObjectHelper, $urlFactory, $moduleManager, $resultJsonFactory, $vendor);
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
