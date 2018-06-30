<?php
namespace Ced\CsMarketplace\Model\System\Message;

class License implements \Magento\Framework\Notification\MessageInterface
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    protected $_objectManager;

    /**
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_objectManager = $objectInterface;
    }
    public function getIdentity()
    {
        // Retrieve unique message identity
        return md5('license');
    }

    public function isDisplayed()
    {
        $configValue = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced/csmarketplace/islicensevalid');
        return is_null($configValue) || $configValue=='' ? false : true ;
    }

    public function getText()
    {
        return __('Invalid License <a href="%1">Activate now</a>',$this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/cedcore/'));
    }

    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}