<?php
namespace Ced\CsVendorReview\Controller\Rating\Post;

/**
 * Interceptor class for @see \Ced\CsVendorReview\Controller\Rating\Post
 */
class Interceptor extends \Ced\CsVendorReview\Controller\Rating\Post implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Ced\CsVendorReview\Model\Review $model, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $model, $customerSession, $customerRepositoryInterface, $resultJsonFactory, $formKeyValidator, $data);
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
