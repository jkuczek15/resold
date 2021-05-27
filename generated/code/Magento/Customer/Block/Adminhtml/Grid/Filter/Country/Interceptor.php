<?php
namespace Magento\Customer\Block\Adminhtml\Grid\Filter\Country;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Grid\Filter\Country
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Grid\Filter\Country implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\DB\Helper $resourceHelper, \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $collectionFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $resourceHelper, $collectionFactory, $data);
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
