<?php
namespace Magento\Customer\Model\Address;

/**
 * Interceptor class for @see \Magento\Customer\Model\Address
 */
class Interceptor extends \Magento\Customer\Model\Address implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, \Magento\Directory\Helper\Data $directoryData, \Magento\Eav\Model\Config $eavConfig, \Magento\Customer\Model\Address\Config $addressConfig, \Magento\Directory\Model\RegionFactory $regionFactory, \Magento\Directory\Model\CountryFactory $countryFactory, \Magento\Customer\Api\AddressMetadataInterface $metadataService, \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory, \Magento\Customer\Api\Data\RegionInterfaceFactory $regionDataFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor, \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $directoryData, $eavConfig, $addressConfig, $regionFactory, $countryFactory, $metadataService, $addressDataFactory, $regionDataFactory, $dataObjectHelper, $customerFactory, $dataProcessor, $indexerRegistry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate();
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
        }
    }
}
