<?php
namespace Ced\CsMarketplace\Model\System\Message;

class PendingPayments implements \Magento\Framework\Notification\MessageInterface
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
        return md5('PENDING_PAYMENTS');
    }

    public function isDisplayed()
    {
        $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vpayment\Requested')
                        ->getCollection()
                        ->addFieldToFilter('status',array('eq'=>\Ced\CsMarketplace\Model\Vpayment\Requested::PAYMENT_STATUS_REQUESTED));
        $collection->getSelect()->group("vendor_id");

        // Return true to show your message, false to hide it
        if(count($collection) > 0)
            return true;
        else
            return false;
    }

    public function getText()
    {
        // Retrieve message text
       /* return '<b>'.count($collection).'</b>'.__(' Vendor(s) have requested for Payment(s).'.__(' Release Payment(s) from').'<a href="'.$this->_urlBuilder->getUrl('csmarketplace/vpayments/index').'">'.__(' Requested Payments Panel').'</a>');*/
    }

    public function getSeverity()
    {
        // Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
        return self::SEVERITY_CRITICAL;
    }
}