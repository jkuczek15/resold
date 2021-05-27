<?php
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Customer\Api\AccountManagementInterface $accountManagement, \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory, \Magento\Customer\Helper\Address $addressHelper, \Magento\Framework\Stdlib\DateTime $dateTime, \Magento\Framework\Registry $registry, \Magento\Customer\Model\Address\Mapper $addressMapper, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Customer\Model\Logger $customerLogger, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $accountManagement, $groupRepository, $customerDataFactory, $addressHelper, $dateTime, $registry, $addressMapper, $dataObjectHelper, $customerLogger, $data);
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
