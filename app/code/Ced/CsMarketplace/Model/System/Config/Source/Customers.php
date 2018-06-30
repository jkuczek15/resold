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

namespace Ced\CsMarketplace\Model\System\Config\Source;
 
class Customers extends AbstractBlock
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($selected = false)
    {
        $options = array();
        $registeredCustomers = array();
        $customers = $this->_objectManager->get('Magento\Customer\Model\Customer')->getCollection();
        if($selected) {
            $customers->addAttributeToFilter('entity_id', array('eq'=>$selected));
        } else {
            $vendors = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getCollection()->addAttributeToSelect('customer_id');
            if(count($vendors)>0) {
                foreach($vendors as $vendor) {
                    $registeredCustomers[] = $vendor->getCustomerId();
                }
                if(count($registeredCustomers) > 0) {
                    $customers->addAttributeToFilter('entity_id', array('nin'=>$registeredCustomers));
                }
            }
        }
        foreach($customers as $customer) {
            $customer->load($customer->getId());
            $options[] = array('value' => $customer->getId(), 'label'=>$customer->getName()." (".$customer->getEmail().")");
        }
        return $options;
    }

}
