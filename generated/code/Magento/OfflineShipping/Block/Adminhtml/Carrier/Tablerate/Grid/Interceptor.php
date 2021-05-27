<?php
namespace Magento\OfflineShipping\Block\Adminhtml\Carrier\Tablerate\Grid;

/**
 * Interceptor class for @see \Magento\OfflineShipping\Block\Adminhtml\Carrier\Tablerate\Grid
 */
class Interceptor extends \Magento\OfflineShipping\Block\Adminhtml\Carrier\Tablerate\Grid implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory $collectionFactory, \Magento\OfflineShipping\Model\Carrier\Tablerate $tablerate, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $collectionFactory, $tablerate, $data);
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
