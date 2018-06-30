<?php
namespace Ced\CsMarketplace\Model\System\Message;

class PendingVendors implements \Magento\Framework\Notification\MessageInterface
{

/**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

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
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
        $this->_authorization = $authorization;
        $this->_urlBuilder = $urlBuilder;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_objectManager = $objectInterface;
    }
    public function getIdentity()
    {
        // Retrieve unique message identity
        return md5('PENDING_VENDORS');
    }

    public function isDisplayed()
    {
        // Return true to show your message, false to hide it
        if(count($this->_objectManager->create('Ced\CsMarketplace\Helper\Data')->getNewVendors()))
            return true;
        else
            return false;
    }

    public function getText()
    {
        // Retrieve message text
        return '<b>'.count($this->_objectManager->create('Ced\CsMarketplace\Helper\Data')->getNewVendors()).'</b>'.__(' Approval Request for Vendor(s).'.__(' Approve Vendor(s) from').'<a href="'.$this->_urlBuilder->getUrl('csmarketplace/vendor/index').'">'.__(' Manage Vendors').'</a>');
    }

    public function getSeverity()
    {
        // Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
        return self::SEVERITY_MAJOR;
    }
}