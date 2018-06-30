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

namespace Ced\CsMarketplace\Plugin;

class CustomerDelete
{
    
    public $_objectManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $ob)
	{
	   $this->_objectManager = $ob;
	}
		
    public function afterAfterDeleteCommit( \Magento\Customer\Model\Customer $subject, $result)
    {
        $customerId = $result->getId();
        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($customerId);
        if ($vendor && $vendor->getId()) {
            $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->deleteVendorProducts($vendor->getId());
            $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')
                    ->sendAccountEmail(\Ced\CsMarketplace\Model\Vendor::VENDOR_DELETED_STATUS, '', $vendor, 0);
            $vendor->delete();
        }
        return $result;
    }
}