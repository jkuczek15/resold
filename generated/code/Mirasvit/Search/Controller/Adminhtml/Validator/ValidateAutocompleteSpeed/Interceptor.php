<?php
namespace Mirasvit\Search\Controller\Adminhtml\Validator\ValidateAutocompleteSpeed;

/**
 * Interceptor class for @see \Mirasvit\Search\Controller\Adminhtml\Validator\ValidateAutocompleteSpeed
 */
class Interceptor extends \Mirasvit\Search\Controller\Adminhtml\Validator\ValidateAutocompleteSpeed implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($resultJsonFactory, $context);
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
