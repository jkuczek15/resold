<?php
namespace Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr\Revision\History\Grid;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr\Revision\History\Grid
 */
class Interceptor extends \Plumrocket\GDPR\Block\Adminhtml\Cms\Page\Edit\Tab\Gdpr\Revision\History\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Registry $coreRegistry, \Plumrocket\GDPR\Model\ResourceModel\Revision\CollectionFactory $revisionCollectionFactory, \Plumrocket\GDPR\Model\ResourceModel\Revision\History\CollectionFactory $historyCollectionFactory, \Magento\Framework\Data\CollectionFactory $frameworkCollectionFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $coreRegistry, $revisionCollectionFactory, $historyCollectionFactory, $frameworkCollectionFactory, $data);
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
