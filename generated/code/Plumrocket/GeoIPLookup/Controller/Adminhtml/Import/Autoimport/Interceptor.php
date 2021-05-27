<?php
namespace Plumrocket\GeoIPLookup\Controller\Adminhtml\Import\Autoimport;

/**
 * Interceptor class for @see \Plumrocket\GeoIPLookup\Controller\Adminhtml\Import\Autoimport
 */
class Interceptor extends \Plumrocket\GeoIPLookup\Controller\Adminhtml\Import\Autoimport implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Json\Helper\Data $jsonHelper, \Plumrocket\GeoIPLookup\Helper\Data $dataHelper, \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindgeoip, \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry $iptocountry)
    {
        $this->___init();
        parent::__construct($context, $resultJsonFactory, $jsonHelper, $dataHelper, $maxmindgeoip, $iptocountry);
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
