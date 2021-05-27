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
namespace Ced\CsMarketplace\Helper\Vproducts;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class Category extends \Magento\Catalog\Helper\Category
{
    protected $_objectManager = null;
 
    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        CategoryRepositoryInterface $categoryRepositoryInterface
    ) {
    
        $this->_objectManager = $objectManager;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $categoryFactory, $storeManager, $dataCollectionFactory, $categoryRepositoryInterface);
    }

    /**
     * Retrieve current store categories
     *
     * @param  boolean|string $sorted
     * @param  boolean        $asCollection
     * @return Varien_Data_Tree_Node_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection|array
     */
    public function getStoreCategories($parentId=0, $sorted=false, $asCollection=false, $toLoad=true)
    {
            $parent = $parentId;
            $categoryObj = $this->categoryRepository->get($parent);
            $subcategories = $categoryObj->getChildrenCategories();
            return $subcategories;
    }

   
}
