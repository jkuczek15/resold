<?php
namespace Resold\Api\Controller\Messages\Delete;

/**
 * Interceptor class for @see \Resold\Api\Controller\Messages\Delete
 */
class Interceptor extends \Resold\Api\Controller\Messages\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultJsonFactory, $categoryFactory, $productRepositoryInterface, $messagingFactory, $registry);
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
