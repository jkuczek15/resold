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

namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

Class SaveStatusChange implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;

    /**
     * SaveStatusChange constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param RequestInterface $request
     */
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request
    ) {
        $this->_objectManager = $objectManager;
        $this->request = $request;
    }
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $productIds = (array)$this->request->getParam('selected');
        $status = (int)$this->request->getParam('status');
        $store = (int)$this->request->getParam('store') ? (int)$this->request->getParam('store') : 0;
        if($status) {
            $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getCollection()
                            ->addFieldToFilter('product_id', array('in'=>$productIds));
            if (count($collection) > 0) {
                foreach ($collection as $row) {
                    $row->setStoreId($store);
                    $row->setStatus($status);
                }
            }
        }
    }
}

