<?php
namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable;

/**
 * Interceptor class for @see \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable
 */
class Interceptor extends \Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Data\CollectionFactory $collectionFactory, \Magento\Framework\DataObjectFactory $dataObjectFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $collectionFactory, $dataObjectFactory, $data);
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
