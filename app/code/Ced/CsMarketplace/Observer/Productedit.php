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

namespace Ced\CsMarketplace\Observer; 

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

Class Productedit implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    
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
        $productid = $observer->getEvent()->getProduct()->getId();
        $data = $this->request->getPostValue('product');
        if ($data) {
            $vproduct = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()
                            ->addFieldToFilter('product_id', $productid)->getFirstItem();
            if ($vproduct && $vproduct->getId()) {
                $vproduct->setData('name', $data['name']);
                $vproduct->setData('description', $data['description']);
                $vproduct->setData('short_description', $data['short_description']);
                $vproduct->setData('sku', $data['sku']);
                $vproduct->setData('is_in_stock', $data['quantity_and_stock_status']['is_in_stock']);
                $vproduct->setData('qty', $data['quantity_and_stock_status']['qty']);
                $vproduct->setData('price', isset($data['price']) ? $data['price'] : 0);
                $vproduct->save();
            }
        }
    }
}
