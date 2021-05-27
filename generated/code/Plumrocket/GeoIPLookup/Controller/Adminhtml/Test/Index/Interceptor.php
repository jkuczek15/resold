<?php
namespace Plumrocket\GeoIPLookup\Controller\Adminhtml\Test\Index;

/**
 * Interceptor class for @see \Plumrocket\GeoIPLookup\Controller\Adminhtml\Test\Index
 */
class Interceptor extends \Plumrocket\GeoIPLookup\Controller\Adminhtml\Test\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Json\Helper\Data $jsonHelper, \Plumrocket\GeoIPLookup\Model\GeoIPLookup $geoIPLookup)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $jsonHelper, $geoIPLookup);
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
