<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\CsMarketplace\Model;

/**
 * Class Notification used for getting filters
 */
class Notification extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var array $params
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

      
    /**
     * @param \Magento\Framework\Model\Context                  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory                             $customAttributeFactory
     * @param ResourceModel\Vendor                              $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb     $resourceCollection
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\Notification $resource = null,
        ResourceModel\Notification\Collection $resourceCollection = null,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $session,
         array $data = []
    ) {
        $this->customerSession = $session;
        
        $this->_objectManager = $objectManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ced\CsMarketplace\Model\ResourceModel\Notification');
    }

    public function getNotifications(){
        $vendorId = $this->customerSession->getVendorId();

        $notifications = [];
        if($vendorId){
            $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            $urlFactory = $this->_objectManager->create('Magento\Framework\UrlFactory')->create();
            $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
            $block = $this->_objectManager->get(\Magento\Framework\View\LayoutInterface::class)->createBlock('Ced\CsMarketplace\Block\Vendor\Navigation');
            $isFirst = !count($this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('',$vendorId));
            
            if($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS ) {
                if (!$vendor->getProfilePicture()) {
                    
                    $notifications[] = [
                        'url' => $urlFactory->getUrl('csmarketplace/vendor/profile',array('_secure' => true)),
                        'title' => __('Add Profile Picture'),
                        'itag' => 'icon-film icons',
                    ];
                }



                if (!$helper->isShopEnabled($vendor)) {
                    
                    $notifications[] = [
                        'url' => '#',
                        'title' => __('Your Shop is disabled By Admin'),
                        'itag' => 'icon-bell icons',
                    ];
                }


                if (!$vendor->getCompanyLogo()) {
                    
                    $notifications[] = [
                        'url' => $urlFactory->getUrl('csmarketplace/vendor/profile',array('_secure' => true)),
                        'title' => __('Add Company Logo'),
                        'itag' => 'icon-camera icons',
                    ];
                }



                if (!$vendor->getCompanyBanner()) {
                    
                    $notifications[] = [
                        'url' => $urlFactory->getUrl('csmarketplace/vendor/profile',array('_secure' => true)),
                        'title' => __('Add Company Banner'),
                        'itag' => 'icon-bell icons',
                    ];
                }



                if ($isFirst) {
                    
                    $notifications[] = [
                        'url' => $urlFactory->getUrl('csmarketplace/vproducts/new',array('_secure' => true)),
                        'title' => __('Add Your First Product'),
                        'itag' => 'icon-bell icons',
                    ];
                }


                if (!$block->isPaymentDetailAvailable()) {
                    
                    
                }

            }

            

            $notificationsCollection = $this->_objectManager->create('Ced\CsMarketplace\Model\NotificationFactory')
            ->create()->getCollection()
            ->addFieldToFilter('vendor_id',$vendorId)
            ->addFieldToFilter('status',0)->setOrder('id','DESC');
            
            
            foreach($notificationsCollection as $notification){
                $notifications[] = [
                        'url' => $notification->getAction(),
                        'title' => __('%1',$notification->getTitle()),
                        'itag' => $notification->getItag(),
                        'created_at' => $notification->getCreatedAt()
                    ];
            }
        
        }
        
        return $notifications;
    }
}
