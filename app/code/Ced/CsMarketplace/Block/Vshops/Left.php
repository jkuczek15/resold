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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vshops;

class Left extends \Magento\Catalog\Block\Navigation
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->addData(
            [
                'cache_lifetime' => 0,
                'cache_tags' => [],
            ]
        );
    }

    public function getCategoriesHtml($category, $flag = false, $lvl = 0)
    {
        $html = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (is_numeric($category)) {

            $_category = $this->loadCategory($category);
            $_categories = $_category->getChildrenCategories();
        } elseif ($category && $category->getId()) {
            $_categories = $category->getChildrenCategories();
        }
        $category_filter = $this->getRequest()->getParam('cat-fil');

        $curVendorId = $objectManager->get('Magento\Framework\Registry')->registry('current_vendor')->getEntityId();


        $cat_fil = array();
        if (isset($category_filter))
            $cat_fil = explode(',', $category_filter);
        if (count($_categories) > 0) {
            $html = '<ul class="level-' . $lvl . ' vshop-left-cat-filter root-category root-category-wrapper">';
            $level = $lvl + 1;
            foreach ($_categories as $value) {
                if ($count = $objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getProductCountcategory($curVendorId, $value->getId(), \Ced\CsMarketplace\Model\Vproducts::AREA_FRONTEND)) {
                    $html .= '<li class="tree-node">';
                    if ($this->getCategoriesHtml($value->getId(), true, $level)) {
                        $html .= '<img class="tree-ec-icon tree-elbow-plus" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">';
                    } else {
                        //$html .= '<img class="tree-ec-icon tree-elbow-line" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">';
                        $html .= '<img class="tree-ec-icon tree-elbow-end" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">';

                    }
                    $html .= '<input class="cat-fil" onchange="filterProductsByCategory(this)" type="checkbox" name="cat-fil" data-uncheckurl="' . $this->getUncheckFilterUrl($value->getId()) . '" value="' . $this->getCheckFilterUrl($value->getId()) . '" ';
                    if (in_array($value->getId(), $cat_fil))
                        $html .= 'checked="checked"';
                    $html .= '>';
                    $label = $value->getName() . " (" . $count . ")";
                    $html .= '<label>' . $label . '</label>';
                    $html .= $this->getCategoriesHtml($value->getId(), true, $level);
                    $html .= '</li>';
                }
            }
            $html .= '</ul>';


        }
        return $html;
    }

    public function getCheckFilterUrl($category_id)
    {
        $urlParams = array('_current' => true, '_escape' => true, '_use_rewrite' => true);

        $category_filter = $this->getRequest()->getParam('cat-fil');

        if (isset($category_filter)) {
            $cat_fil = explode(',', $category_filter);
            if (!in_array($category_id, $cat_fil)) {
                $urlParams['_query'] = array('cat-fil' => $category_filter . ',' . $category_id);
            }
        } else
            $urlParams['_query'] = array('cat-fil' => $category_id);

        return $this->getUrl('*/*/*', $urlParams);
    }

    public function getUncheckFilterUrl($category_id)
    {
        $urlParams = array('_current' => true, '_escape' => true, '_use_rewrite' => true);

        $category_filter = $this->getRequest()->getParam('cat-fil');

        if (isset($category_filter)) {
            $cat_fil = explode(',', $category_filter);
            if (in_array($category_id, $cat_fil)) {
                $cat_fil = $this->remove_array_item($cat_fil, $category_id);
                if (!count($cat_fil))
                    return trim($this->getBaseUrl(), '/') . rtrim($this->getRequest()->getOriginalPathInfo(), '/');
                elseif (count($cat_fil) > 0)
                    $urlParams['_query'] = array('cat-fil' => implode(',', $cat_fil));
            }
        }
        return $this->getUrl('*/*/*', $urlParams);
    }

    public function remove_array_item($array, $item)
    {
        $index = array_search($item, $array);
        if ($index !== false) {
            unset($array[$index]);
        }

        return $array;
    }

    public function loadCategory($id)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get('Magento\Catalog\Model\Category')->load($id);
    }

    public function getStore()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null);
    }


}
