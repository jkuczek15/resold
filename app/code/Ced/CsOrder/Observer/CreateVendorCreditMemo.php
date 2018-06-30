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
  * @category  Ced
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsOrder\Observer;
use Magento\Framework\Event\ObserverInterface;

Class CreateVendorCreditmemo implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    
    public function __construct(
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsOrder\Helper\Data $helper,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Ced\CsOrder\Model\Creditmemo $vcreditmemo,
        \Ced\CsMarketplace\Helper\Data $marketplacehelper
    ) {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->vproducts = $vproducts;
        $this->vcreditmemo = $vcreditmemo;
        $this->marketplacehelper = $marketplacehelper;
    }
    /**
     *Set vendor naem and url to product incart
     *
     *@param $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if($this->helper->isActive()) {
                $creditmemo = $observer->getCreditmemo();
                $allItems = $creditmemo->getAllItems();
                $creditmemoVendor = [];
                foreach($allItems as $item){
                    $vendorId = $this->vproducts->getVendorIdByProduct($item->getProductId());
                    $creditmemoVendor[$vendorId] = $vendorId;
                }

                foreach($creditmemoVendor as $vendorId){
                    try{
                        $id = $creditmemo->getId();
                        $vCreditmemo = $this->vcreditmemo;
                        $vCreditmemo->setCreditmemoId($id);
                        $vCreditmemo->setVendorId($vendorId);
                        $vCreditmemo->save();
                    }catch(\Exception $e){
                        $this->marketplacehelper->logException($e);
                    }
                }
            }
        } catch(\Exception $e) {
            $this->marketplacehelper->logException($e);
        }
    }
}  
