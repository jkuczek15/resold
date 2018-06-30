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

namespace Ced\CsMarketplace\Block\Vendor\Dashboard;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResourceConnection;

class MostSoldProducts extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	
	protected $_collectionFactory;
	
	public $resourceConnection;
	
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
 public function __construct(
        Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,		
		UrlFactory $urlFactory,
 		\Magento\Catalog\Model\Product $collectionFactory,
 		ResourceConnection $resourceConnection
    ){
 		$this->_collectionFactory = $collectionFactory;
	        $this->resourceConnection = $resourceConnection;
		 parent::__construct($context,$customerSession,$objectManager,$urlFactory);
    }

    
    public function getBestSellerProducts()
    {
    	$collection = $this->_collectionFactory
    	->getCollection()
    	->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1')
    	->addAttributeToFilter('type_id', array('eq' => 'simple'))
    	->addAttributeToSelect('country')
    	->addAttributeToSelect('region')
    	->addAttributeToSelect('grape_varieties');
    
    
    	$collection->getSelect()
    	->joinLeft(
    			array('item_table' => $this->resourceConnection->getTableName('sales_order_item')),
    			'entity_id = item_table.product_id',
    			array('qty_ordered' => 'SUM(item_table.qty_ordered)')
    	)
    	->group('entity_id')
    	->order('qty_ordered ' . 'DESC');
    
    	$collection->getSelect()
    	->join(
    			array('vendor_table' => $this->resourceConnection->getTableName('ced_csmarketplace_vendor_products')),
    			'entity_id = vendor_table.product_id',
    			array('entity_id' => 'e.entity_id',
    					'vendor_id' => 'vendor_table.vendor_id',
    					'price' => 'vendor_table.price',
    					'name' => 'vendor_table.name')
    	);
    	
    		$collection->getSelect()->where(
    				'vendor_table.vendor_id IN (?)',
    				$this->getVendorId()
    		);
    	
    
    	$collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
    	$collection->getSelect()->limit(5);
    	return $collection;
    }
	
}
