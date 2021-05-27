<?php
namespace MSP\TwoFactorAuth\Block\Provider\Authy\Configure;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Block\Provider\Authy\Configure
 */
class Interceptor extends \MSP\TwoFactorAuth\Block\Provider\Authy\Configure implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \MSP\TwoFactorAuth\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $countryCollectionFactory, $data);
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
