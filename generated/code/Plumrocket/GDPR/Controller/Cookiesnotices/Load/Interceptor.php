<?php
namespace Plumrocket\GDPR\Controller\Cookiesnotices\Load;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Controller\Cookiesnotices\Load
 */
class Interceptor extends \Plumrocket\GDPR\Controller\Cookiesnotices\Load implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Plumrocket\GDPR\Helper\Data $dataHelper, \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $resultPageFactory, $dataHelper, $geoLocationHelper);
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
