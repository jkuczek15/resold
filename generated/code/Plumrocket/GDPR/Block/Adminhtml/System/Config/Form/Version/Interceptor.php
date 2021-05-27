<?php
namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Version;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Version
 */
class Interceptor extends \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Version implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Plumrocket\Base\Helper\Base $baseHelper, \Magento\Framework\Module\ModuleListInterface $moduleList, \Magento\Framework\Module\Manager $moduleManager, \Magento\Store\Model\StoreManager $storeManager, \Magento\Framework\App\ProductMetadataInterface $productMetadata, \Magento\Framework\HTTP\PhpEnvironment\ServerAddress $serverAddress, \Magento\Framework\App\Cache\Proxy $cacheManager, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $this->___init();
        parent::__construct($baseHelper, $moduleList, $moduleManager, $storeManager, $productMetadata, $serverAddress, $cacheManager, $objectManager, $context, $data);
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
