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
namespace Ced\CsMarketplace\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

Class SalesQuoteItemSetVendorId implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request,
        ManagerInterface $messageManager
    ) {
        $this->_objectManager = $objectManager;
        $this->request = $request;
        $this->messageManager = $messageManager;
        
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $vProducts = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts');

        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
     
        if($vendorId = $vProducts->getVendorIdByProduct($item->getProductId())) {
            $item->setVendorId($vendorId);
            if($product->getTypeId()=='configurable') {
                return ;
            }
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            $publicName = $vendor->getPublicName();
            $shopUrl = $vendor->getVendorShopUrl();
                
            $additionalOptions = array(array(
              'code'  => 'my_code',
              'label'  => 'Vendor',
              'value' => $publicName
            ));

            //START BACKWORD COMPATABILITY CODE 
            $compatability = class_exists(
                            "\\Magento\\Framework\\Serialize\\Serializer\\Json")? $this->_objectManager->create('\Magento\Framework\Serialize\Serializer\Json')->serialize($additionalOptions) : serialize($additionalOptions);
            // END BACKWARD COMPATABILITY CODE   
            $item->addOption(
                    new \Magento\Framework\DataObject(
                        [
                            'product' => $item->getProduct(),
                            'code' => 'additional_options',
                            'value' => $compatability
                        ]
                    )
                );
        }       

    }
}
