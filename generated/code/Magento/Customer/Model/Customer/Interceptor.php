<?php
namespace Magento\Customer\Model\Customer;

/**
 * Interceptor class for @see \Magento\Customer\Model\Customer
 */
class Interceptor extends \Magento\Customer\Model\Customer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Eav\Model\Config $config, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Customer\Model\ResourceModel\Customer $resource, \Magento\Customer\Model\Config\Share $configShare, \Magento\Customer\Model\AddressFactory $addressFactory, \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressesFactory, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Customer\Api\GroupRepositoryInterface $groupRepository, \Magento\Framework\Encryption\EncryptorInterface $encryptor, \Magento\Framework\Stdlib\DateTime $dateTime, \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory, \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Customer\Api\CustomerMetadataInterface $metadataService, \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $storeManager, $config, $scopeConfig, $resource, $configShare, $addressFactory, $addressesFactory, $transportBuilder, $groupRepository, $encryptor, $dateTime, $customerDataFactory, $dataObjectProcessor, $dataObjectHelper, $metadataService, $indexerRegistry, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function afterDeleteCommit()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'afterDeleteCommit');
        if (!$pluginInfo) {
            return parent::afterDeleteCommit();
        } else {
            return $this->___callPlugins('afterDeleteCommit', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save();
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
