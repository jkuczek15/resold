<?php
namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons\AbstractBlock;

/**
 * Interceptor class for @see \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons\AbstractBlock
 */
class Interceptor extends \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons\AbstractBlock implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Module\ModuleListInterface $moduleList, \Magento\Framework\Module\Manager $moduleManager, \Magento\Store\Model\StoreManager $storeManager, \Magento\Framework\App\ProductMetadataInterface $productMetadata, \Magento\Framework\HTTP\PhpEnvironment\ServerAddress $serverAddress, \Magento\Framework\App\Cache\Proxy $cacheManager, \Plumrocket\Base\Helper\Data $baseHelper, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($moduleList, $moduleManager, $storeManager, $productMetadata, $serverAddress, $cacheManager, $baseHelper, $context, $data);
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
