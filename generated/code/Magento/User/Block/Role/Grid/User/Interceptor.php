<?php
namespace Magento\User\Block\Role\Grid\User;

/**
 * Interceptor class for @see \Magento\User\Block\Role\Grid\User
 */
class Interceptor extends \Magento\User\Block\Role\Grid\User implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Registry $coreRegistry, \Magento\Authorization\Model\RoleFactory $roleFactory, \Magento\User\Model\ResourceModel\Role\User\CollectionFactory $userRolesFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $jsonEncoder, $coreRegistry, $roleFactory, $userRolesFactory, $data);
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
