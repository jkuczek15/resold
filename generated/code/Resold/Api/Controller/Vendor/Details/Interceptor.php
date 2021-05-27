<?php
namespace Resold\Api\Controller\Vendor\Details;

/**
 * Interceptor class for @see \Resold\Api\Controller\Vendor\Details
 */
class Interceptor extends \Resold\Api\Controller\Vendor\Details implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Ced\CsMarketplace\Model\VendorFactory $Vendor)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultJsonFactory, $Vendor);
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
