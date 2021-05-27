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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block;

class Links extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
    /**
     * Get links MarketPlace
     *
     * @return \Magento\Framework\View\Element\Html\Link[]
     */
    public function getLinks()
    {
        $childBlocks = $this->_layout->getChildBlocks($this->getNameInLayout());
        return $childBlocks;
    }

    /**
     * Find link by path CsMarketPlace
     *
     * @param string $path
     * @return \Magento\Framework\View\Element\Html\Link
     */
    protected function getLinkByPath($path)
    {
        $var = '';
        foreach ($this->getLinks() as $cedlink) {
            if ($cedlink->getPath() == $path) {
                $var = $cedlink;
                break;
            }
        }
        return $var;
    }

    /**
     * Set active link CsMarketPlace
     *
     * @param string $path
     * @return void
     */
    public function setActive($path)
    {
        $cedlink = $this->getLinkByPath($path);
        if ($cedlink) {
            $cedlink->setIsHighlighted(true);
        }
    }

    /**
     * Render Block CsMarketPlace
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $link
     * @return string
     */
    public function renderLink(\Magento\Framework\View\Element\AbstractBlock $link)
    {
    	$allowedType = [];
    	$config = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface')
    	->getValue('ced_vproducts/general/type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	if($config){
    		$allowedType = explode(',', $config);
    	}
    	
    	if($link->getNameInLayout() == 'simple_product_creation' && !in_array('simple', $allowedType)){
    		return '';
    	}
    	elseif($link->getNameInLayout() == 'configurable_product_creation' && !in_array('configurable', $allowedType)){
    		return '';
    	}
    	elseif($link->getNameInLayout() == 'bundle_product_creation' && !in_array('bundle', $allowedType)){
    		return '';
    	}
    	elseif($link->getNameInLayout() == 'virtual_product_creation' && !in_array('virtual', $allowedType)){
    		return '';
    	}
    	elseif($link->getNameInLayout() == 'downloadable_product_creation' && !in_array('downloadable', $allowedType)){
    		return '';
    	}
    	elseif($link->getNameInLayout() == 'grouped_product_creation' && !in_array('grouped', $allowedType)){
    		return '';
    	}
        $renderElement = $this->_layout->renderElement($link->getNameInLayout());
        return $renderElement;
    }

    /**
     * Render block HTML CsMarketPlace
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $cedhtml = '';
        if ($this->getLinks()) {
            $cedhtml = '<ul' . ($this->hasCssClass() ? ' class="' . $this->escapeHtml(
                $this->getCssClass()
            ) . '"' : '') . '>';

            foreach ($this->getLinks() as $cedlink) {
                $cedhtml .= $this->renderLink($cedlink);
            }
            $cedhtml .= '</ul>';
        }

        return $cedhtml;
    }
    
    /**
     * Get customer seesion for CsMarketPlace
     *
     * @return Mage_Customer_Model_Session
     */
    public function _getSession() {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }
}