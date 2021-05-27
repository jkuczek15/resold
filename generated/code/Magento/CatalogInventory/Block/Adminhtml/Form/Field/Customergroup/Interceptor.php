<?php
namespace Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup;

/**
 * Interceptor class for @see \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup
 */
class Interceptor extends \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Customergroup implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Customer\Api\GroupManagementInterface $groupManagement, \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $groupManagement, $groupRepository, $searchCriteriaBuilder, $data);
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
