<?php
namespace Resold\Api\Controller\Product\Customer;

/**
 * Interceptor class for @see \Resold\Api\Controller\Product\Customer
 */
class Interceptor extends \Resold\Api\Controller\Product\Customer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Ced\CsMarketplace\Model\VendorFactory $Vendor)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $categoryFactory, $Vendor);
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
