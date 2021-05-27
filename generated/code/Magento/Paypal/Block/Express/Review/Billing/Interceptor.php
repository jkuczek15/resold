<?php
namespace Magento\Paypal\Block\Express\Review\Billing;

/**
 * Interceptor class for @see \Magento\Paypal\Block\Express\Review\Billing
 */
class Interceptor extends \Magento\Paypal\Block\Express\Review\Billing implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Checkout\Model\Session $resourceSession, \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Framework\App\Http\Context $httpContext, \Magento\Quote\Model\Quote\AddressFactory $addressFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resourceSession, $countryCollectionFactory, $customerRepository, $httpContext, $addressFactory, $data);
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
