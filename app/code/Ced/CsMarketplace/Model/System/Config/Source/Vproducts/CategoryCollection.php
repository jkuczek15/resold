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

namespace Ced\CsMarketplace\Model\System\Config\Source\Vproducts;
 
class CategoryCollection
{
    
    protected $_categoryFactory;
    
    private $options;
    
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
    }
    
    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->options = array();
        $collection = $this->_categoryFactory->create()->getCollection()
            ->addAttributeToSelect(['name', 'is_active', 'parent_id', 'level', 'children'])
            ->addAttributeToFilter('entity_id', ['neq' => \Magento\Catalog\Model\Category::TREE_ROOT_ID]);
                
        $categoryById = [
            \Magento\Catalog\Model\Category::TREE_ROOT_ID => [
                'id' => \Magento\Catalog\Model\Category::TREE_ROOT_ID,
                'children' => [],
            ],
        ];
        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['id' => $categoryId, 'children' => []];
                }
            }
            $categoryById[$category->getId()]['is_active'] = $category->getIsActive();
            $categoryById[$category->getId()]['label'] = $category->getName();
            $categoryById[$category->getId()]['level'] = $category->getLevel();
            $categoryById[$category->getParentId()]['children'][] = & $categoryById[$category->getId()];
        }    
                    
        $this->renederCat($categoryById[\Magento\Catalog\Model\Category::TREE_ROOT_ID]['children']);            
        return $this->options;        
    }
    
    
    public function renederCat($data)
    {
        foreach($data as $cat){
            $this->options[] = array('value' => $cat['id'], 'label'=>__($cat['label']));
            if($cat['children']) {
                $this->renederCat($cat['children']);
            }            
        }
    }
}
