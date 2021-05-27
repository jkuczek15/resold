<?php
namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes
 */
class Interceptor extends \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Config\Model\Config\Source\Yesno $yesno, \Plumrocket\GDPR\Model\Config\Source\Pages $pages, \Plumrocket\GDPR\Model\Config\Source\ConsentLocations $consentLocations, \Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions $geoIPRestrictions, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($yesno, $pages, $consentLocations, $geoIPRestrictions, $context, $data);
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
