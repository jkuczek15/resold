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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vendor\Dashboard;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Info extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    public $_priceCurrencyInterface;


    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $urlModel;

    public $_localeCurrency;

    /**
     * Info constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Locale\Currency $localeCurrency
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\Currency $localeCurrency,
        UrlFactory $urlFactory,
        PriceCurrencyInterface $priceCurrencyInterface
    )
    {
        $this->urlModel = $urlFactory;
        $this->_objectManager = $objectManager;
        $this->_localeCurrency = $localeCurrency;
        $this->_priceCurrencyInterface = $priceCurrencyInterface;
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
    }

    public function getHelper($class)
    {
        return $this->_objectManager->get($class);
    }


    /**
     * Get vendor's Products data
     * @return array
     */
    public function getVendorProductsData()
    {
        // Total Pending Products
        $data = array('total' => array(), 'action' => []);
        if ($vendorId = $this->getVendorId()) {
            $vproducts = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts');
            $pendingProducts = count($vproducts->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS, $vendorId));
            $approvedProducts = count($vproducts->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS, $vendorId));
            $disapprovedProducts = count($vproducts->getVendorProducts(\Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS, $vendorId));

            if ($pendingProducts > 1000000000000) {
                $data['total'][] = round($pendingProducts / 1000000000000, 1) . 'T';
            } elseif ($pendingProducts > 1000000000) {
                $data['total'][] = round($pendingProducts / 1000000000, 1) . 'B';
            } elseif ($pendingProducts > 1000000) {
                $data['total'][] = round($pendingProducts / 1000000, 1) . 'M';
            } elseif ($pendingProducts > 1000) {
                $data['total'][] = round($pendingProducts / 1000, 1) . 'K';
            } else {
                $data['total'][] = round($pendingProducts);
            }
            $data['action'][] = $this->getUrl('*/vproducts/', array('_secure' => true, 'check_status' => 2));

            if ($approvedProducts > 1000000000000) {
                $data['total'][] = round($approvedProducts / 1000000000000, 1) . 'T';
            } elseif ($approvedProducts > 1000000000) {
                $data['total'][] = round($approvedProducts / 1000000000, 1) . 'B';
            } elseif ($approvedProducts > 1000000) {
                $data['total'][] = round($approvedProducts / 1000000, 1) . 'M';
            } elseif ($approvedProducts > 1000) {
                $data['total'][] = round($approvedProducts / 1000, 1) . 'K';
            } else {
                $data['total'][] = round($approvedProducts);
            }
            $data['action'][] = $this->getUrl('*/vproducts/', array('_secure' => true, 'check_status' => 1));

            if ($disapprovedProducts > 1000000000000) {
                $data['total'][] = round($disapprovedProducts / 1000000000000, 1) . 'T';
            } elseif ($disapprovedProducts > 1000000000) {
                $data['total'][] = round($disapprovedProducts / 1000000000, 1) . 'B';
            } elseif ($disapprovedProducts > 1000000) {
                $data['total'][] = round($disapprovedProducts / 1000000, 1) . 'M';
            } elseif ($disapprovedProducts > 1000) {
                $data['total'][] = round($disapprovedProducts / 1000, 1) . 'K';
            } else {
                $data['total'][] = round($disapprovedProducts);
            }
            $data['action'][] = $this->getUrl('*/vproducts/', array('_secure' => true, 'check_status' => 0));
        }
        return $data;
    }
    
    public function getVendorProducts(){
        $collection = $this->getObjectManager()->get('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('vendor_id',$this->getVendorId())->setOrder('id','DESC')->setPageSize(5)->setCurPage(1);
        return $collection;
    }
}
