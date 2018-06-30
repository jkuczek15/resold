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
 * @package   Ced_CsMarketplace
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;

Class ChangeNewProductStatus implements ObserverInterface
{
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectManager;
	
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

	/**
	 *Notify Customer Account share Change 
	 *
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$product = $observer->getEvent()->getProduct();
		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		$confirmation = $scopeConfig->getValue('ced_vproducts/general/confirmation', 'store', $storeManager->getStore()->getCode());
	
		$marketplaceProduct = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->load($product->getId(),'product_id');

		if($marketplaceProduct && $marketplaceProduct->getId()) {
			if ($marketplaceProduct->getCheckStatus() == \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS) {
				$status = $product->getStatus();
			} else {
				$status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
			}
		} else {
			if ($confirmation == true) {
				$status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
			} else {
				$status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
			}
		}
		$this->_objectManager->create('Magento\Catalog\Model\Product\Action')
			->updateAttributes([$product->getId()], ['status' => $status], $product->getStoreId());
	}
}
