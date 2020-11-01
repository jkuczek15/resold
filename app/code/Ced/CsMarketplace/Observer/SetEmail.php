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

Class SetEmail implements ObserverInterface
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
        $customer = $observer->getCustomer();
		$vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($customer->getId());
		if ($vendor && $vendor->getEmail() != $customer->getEmail()) {
			$vendor->setSettingFromCustomer(true)->setEmail($customer->getEmail())->save();
		}
	}
}
