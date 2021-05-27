<?php
namespace Magento\Multishipping\Block\Checkout\Address\Select;

/**
 * Interceptor class for @see \Magento\Multishipping\Block\Checkout\Address\Select
 */
class Interceptor extends \Magento\Multishipping\Block\Checkout\Address\Select implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Multishipping\Model\Checkout\Type\Multishipping $multishipping, \Magento\Customer\Helper\Address $customerAddressHelper, \Magento\Customer\Model\Address\Mapper $addressMapper, \Magento\Customer\Api\AddressRepositoryInterface $addressRepository, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Framework\Api\FilterBuilder $filterBuilder, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $multishipping, $customerAddressHelper, $addressMapper, $addressRepository, $searchCriteriaBuilder, $filterBuilder, $data);
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
