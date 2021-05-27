<?php
namespace Resold\Api\Controller\Region\Index;

/**
 * Interceptor class for @see \Resold\Api\Controller\Region\Index
 */
class Interceptor extends \Resold\Api\Controller\Region\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Directory\Model\RegionFactory $regionFactory)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultJsonFactory, $regionFactory);
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
