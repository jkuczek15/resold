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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model;

/**
 * CsMarketplace session model
 */
class Session extends \Magento\Customer\Model\Session
{

    public function getVendor()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        return $objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($this->getVendorId());
    }

    public function getVendorId()
    {
        return $this->getCustomerSession()->getVendorId();
    }

    public function getCustomerSession()
    {
    	if (!$this->_session->getCustomerId()) {
    		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
    		$this->_session = $objectManager->create('Magento\Customer\Model\SessionFactory')->create();
    	}
        return $this->_session;
    }

}
