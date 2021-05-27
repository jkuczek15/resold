<?php
namespace Plumrocket\GDPR\Controller\Cookiesnotices\Log;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Cookiesnotices\Log
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Cookiesnotices\Log implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Plumrocket\GDPR\Helper\Data $dataHelper, \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $dataHelper, $checkboxesHelper);
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
