<?php
namespace Ced\CsMarketplace\Controller\Account\CreatePost;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Account\CreatePost
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Account\CreatePost implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Api\AccountManagementInterface $vendorAcManagement, \Magento\Customer\Helper\Address $csAddressHelper, \Magento\Framework\UrlFactory $urlFactory, \Magento\Customer\Model\Metadata\FormFactory $csFormFactory, \Magento\Newsletter\Model\SubscriberFactory $csSubscriberFactory, \Magento\Customer\Api\Data\RegionInterfaceFactory $csRegionDataFactory, \Magento\Customer\Api\Data\AddressInterfaceFactory $csAddressDataFactory, \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory, \Ced\CsMarketplace\Model\Url $vendorUrl, \Magento\Customer\Model\Registration $vendorRegistration, \Magento\Framework\Escaper $csEscaper, \Magento\Customer\Model\CustomerExtractor $customerExtractor, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Ced\CsMarketplace\Model\Account\Redirect $accountRedirect, \Ced\CsMarketplace\Model\VendorFactory $Vendor, \Ced\CsMarketplace\Helper\Data $datahelper)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $scopeConfig, $storeManager, $vendorAcManagement, $csAddressHelper, $urlFactory, $csFormFactory, $csSubscriberFactory, $csRegionDataFactory, $csAddressDataFactory, $customerDataFactory, $vendorUrl, $vendorRegistration, $csEscaper, $customerExtractor, $dataObjectHelper, $accountRedirect, $Vendor, $datahelper);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
