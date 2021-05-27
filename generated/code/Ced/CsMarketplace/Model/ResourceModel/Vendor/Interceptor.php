<?php
namespace Ced\CsMarketplace\Model\ResourceModel\Vendor;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Model\ResourceModel\Vendor
 */
class Interceptor extends \Ced\CsMarketplace\Model\ResourceModel\Vendor implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Eav\Model\Entity\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Validator\Factory $validatorFactory, \Magento\Framework\Stdlib\DateTime $dateTime, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Store\Model\StoreManagerInterface $storeManager, $data = [])
    {
        $this->___init();
        parent::__construct($context, $scopeConfig, $validatorFactory, $dateTime, $objectManager, $storeManager, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save($object);
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'delete');
        if (!$pluginInfo) {
            return parent::delete($object);
        } else {
            return $this->___callPlugins('delete', func_get_args(), $pluginInfo);
        }
    }
}
