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

namespace Ced\CsMarketplace\Model\Vendor;

class Form extends \Magento\Framework\Model\AbstractModel
{


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\Form $resource = null,
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\Form\Collection  $resourceCollection = null,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        array $data = []
    ) {
            
        $this->_objectManager = $objectInterface;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ced\CsMarketplace\Model\ResourceModel\Vendor\Form');
    }
    
    public function insertMultiple($feedData = array())
    {
        $coreResource   = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection'); 
        $feedTable      = $coreResource->getTableName('ced_csmarketplace_vendor_form_attribute');
        $conn = $coreResource->getConnection('write');
        if($conn->insertMultiple($feedTable, $feedData)) {
            return true; 
        }
        return false;
    }
    
}
