<?php
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs;

/**
 * Interceptor class for @see \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs
 */
class Interceptor extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Framework\Module\Manager $moduleManager, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $collectionFactory, \Magento\Catalog\Helper\Catalog $helperCatalog, \Magento\Catalog\Helper\Data $catalogData, \Magento\Framework\Registry $registry, \Magento\Framework\Translate\InlineInterface $translateInline, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $authSession, $moduleManager, $collectionFactory, $helperCatalog, $catalogData, $registry, $translateInline, $data);
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
