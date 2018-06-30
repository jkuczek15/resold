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
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;
use Ced\CsMarketplace\Model\Session as MarketplaceSession;
use Ced\CsMarketplace\Model\Vorders as vendorOrder;

class Vorders extends Vendor
{

    /**
     * @var Session
     */
    protected $session;

    public $marketplacesession;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */


    public $_coreRegistry = null;

    public $_vorders;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        Manager $moduleManager,
        Registry $registry,
        MarketplaceSession $mktSession,
        vendorOrder $vorders
    ) {
        $this->_vorders = $vorders;
        $this->_coreRegistry = $registry;
        $this->marketplacesession = $mktSession;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);

    }

    /**
     * Check order view availability
     * @param $vorder
     * @return bool
     */
    protected function _canViewOrder($vorder)
    {
        if(!$this->_getSession()->getVendorId()) {
            return false;

        }
        $vendorId = $this->marketplacesession->getVendorId();
         
        $incrementId = $vorder->getOrder()->getIncrementId();
        
        $collection =  $this->_vorders->create()->getCollection();
        $collection->addFieldToFilter('id', $vorder->getId())
            ->addFieldToFilter('order_id', $incrementId)
            ->addFieldToFilter('vendor_id', $vendorId);
        
        if(count($collection)>0) {
            return true;
        }else{
            return false;
        }
        return false;
    }

    /**
     * Try to load valid order by order_id and register it
     * @param null $orderId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _loadValidOrder($orderId = null)
    {
        if (null === $orderId) {
            $orderId = (int) $this->getRequest()->getParam('order_id', 0);
        }
        $incrementId = 0;
        if ($orderId == 0) {
            $incrementId = (int) $this->getRequest()->getParam('increment_id', 0);
            if (!$incrementId) {
                $this->_forward('noRoute');
                return false;
            }
        }

        if($orderId) {
            $vorder = $this->_vorders->load($orderId);
        }
        else if($incrementId) {
            $vendorId = $this->marketplacesession->getVendorId();
            $vorder = $this->_vorders->loadByField(['order_id','vendor_id'], [$incrementId,$vendorId]);
        }
        $order = $vorder->getOrder();
        if ($this->_canViewOrder($vorder)) {
            $this->_coreRegistry->register('current_order', $order);
            $this->_coreRegistry->register('current_vorder', $vorder);
            return true;
        } else {
            $this->_redirect('*/*');
        }
        return false;
    }
    
}
