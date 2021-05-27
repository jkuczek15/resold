<?php
namespace Magento\User\Block\Role\Tab\Edit;

/**
 * Interceptor class for @see \Magento\User\Block\Role\Tab\Edit
 */
class Interceptor extends \Magento\User\Block\Role\Tab\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever, \Magento\Framework\Acl\RootResource $rootResource, \Magento\Authorization\Model\ResourceModel\Rules\CollectionFactory $rulesCollectionFactory, \Magento\Framework\Acl\AclResource\ProviderInterface $aclResourceProvider, \Magento\Integration\Helper\Data $integrationData, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $aclRetriever, $rootResource, $rulesCollectionFactory, $aclResourceProvider, $integrationData, $data);
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
