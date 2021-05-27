<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Vendor;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Navigation extends \Ced\CsMarketplace\Controller\Vendor
{

    public $resultJsonFactory;
    public $urlFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->urlFactory = $urlFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }


    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */


    public function execute()
    {

        $result = [];
        if (!$this->_getSession()->getVendorId()) {
            
        }
        else
        {
            $result['notifications'] = $this->_objectManager->get('Ced\CsMarketplace\Model\NotificationHandler')->getNotifications();
            $resultJson = $this->resultJsonFactory->create();
            $vendorId = $this->_getSession()->getVendorId();
            $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Tool\Image');
            $block = $this->_objectManager->get(\Magento\Framework\View\LayoutInterface::class)->createBlock('Ced\CsMarketplace\Block\Vendor\Navigation\Statatics','csmarketplace_vendor_navigation_statatics_header');
            $block->getVendorAttributeInfo();

            $percent = round(($block->getSavedAttr() * 100)/$block->getTotalAttr());
            $href = '#';
            $urlFactory = $this->urlFactory->create();
            if($vendorId && $vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS ){
                $href = $urlFactory->getUrl('csmarketplace/vendor/profile',array('_secure' => true));
            } 
            $result['statistics']['percent'] = $percent ;
            $result['statistics']['href'] = $href;
            $result['vendor']['profile_pic'] = $helper->getResizeImage($vendor->getData('profile_picture'), 'logo', 50, 50);
            $result['vendor']['is_approved'] = 0;
            $result['vendor']['name'] = $vendor->getName();
            if($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
                $result['vendor']['is_approved'] = 1;
                $result['vendor']['status'] = __('Approved');
                $result['vendor']['status_itag'] = 'fa fa-circle text-success';

            } elseif($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_DISAPPROVED_STATUS) {
                $result['vendor']['status'] = __('Disapproved');
                $result['vendor']['status_itag'] = 'fa fa-circle text-danger';
            } else {
                $result['vendor']['status'] = __('New');;
                $result['vendor']['status_itag'] = 'fa fa-circle text-warning';
            }
            $result['vendor']['profile_url'] = $urlFactory->getUrl('csmarketplace/vendor/profileview/',array('_secure'=>true));
            $result['vendor']['settings_url'] = $urlFactory->getUrl('csmarketplace/vsettings/',array('_secure'=>true));
            $result['vendor']['logout_url'] = $urlFactory->getUrl('csmarketplace/account/logout/',array('_secure'=>true)); 

            $result['vendor']['shop_url'] = $vendor->getVendorShopUrl();
        }
        $resultJson->setData($result);
        return $resultJson;
     }
}
