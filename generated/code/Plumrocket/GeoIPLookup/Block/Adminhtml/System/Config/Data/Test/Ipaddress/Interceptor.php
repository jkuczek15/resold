<?php
namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Test\Ipaddress;

/**
 * Interceptor class for @see \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Test\Ipaddress
 */
class Interceptor extends \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Test\Ipaddress implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($remoteAddress, $context, $data);
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
