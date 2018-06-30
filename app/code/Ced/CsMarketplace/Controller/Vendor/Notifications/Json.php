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

namespace Ced\CsMarketplace\Controller\Vendor\Notifications;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Json extends \Ced\CsMarketplace\Controller\Vendor
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
            $size = $this->getRequest()->getParam('size');
            $page = $this->getRequest()->getParam('page');
            $notifications = $this->_objectManager->get('Ced\CsMarketplace\Model\Notification')
                                ->getCollection()
                                ->addFieldToFilter('vendor_id',$this->_getSession()->getVendorId())
                                //->addFieldToFilter('status',0)
                                ->setPageSize($size)
                                ->setCurPage($page)
                                ->setOrder('id','DESC')
                                ->toArray();
            $resultJson = $this->resultJsonFactory->create();
            $resultJson->setData($notifications);
            
        }
        
        return $resultJson;
     }
}
