<?php
namespace Resold\Api\Controller\Product\Delete;

/**
 * Interceptor class for @see \Resold\Api\Controller\Product\Delete
 */
class Interceptor extends \Resold\Api\Controller\Product\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultJsonFactory, $categoryFactory, $productRepositoryInterface, $registry);
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
