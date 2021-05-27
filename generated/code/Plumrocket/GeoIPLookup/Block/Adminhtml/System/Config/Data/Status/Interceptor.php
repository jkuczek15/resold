<?php
namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Status;

/**
 * Interceptor class for @see \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Status
 */
class Interceptor extends \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Status implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Plumrocket\GeoIPLookup\Helper\Data $dataHelper, \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry $ipToCountry, \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindGeoIp, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($dataHelper, $ipToCountry, $maxmindGeoIp, $context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}
