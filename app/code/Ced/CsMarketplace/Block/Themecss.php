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
class Themecss extends \Magento\Framework\View\Element\Template {
	CONST NEW_SELLER_THEME = 'Ced/ced_2k18';
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context
		)
		{
			parent::__construct($context);
		}
		
	protected function _construct() {
		$appliedTheme = $this->_scopeConfig->getValue('ced_csmarketplace/general/theme',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		if($appliedTheme==self::NEW_SELLER_THEME){
			$themeColor = $this->_scopeConfig->getValue('ced_csmarketplace/general/theme_color',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);	
			$this->pageConfig->addPageAsset('css/seller-reg.css');
			$this->pageConfig->addPageAsset('css/color/'.$themeColor);
		}
	}
}