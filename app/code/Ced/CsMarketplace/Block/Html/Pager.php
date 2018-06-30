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

namespace Ced\CsMarketplace\Block\Html;

class Pager extends \Magento\Theme\Block\Html\Pager
{
	protected $_objectManager;
	protected $_moduleManager;
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager
	)
    { 
		$this->_objectManager = $objectManager;
		parent::__construct($context);
		
	}
	
    protected function _construct()
    {  
        parent::_construct();
        $this->setData('show_amounts', true);
        $this->setData('use_container', true);
       
    }
    
    public function getLimitUrl($limit)
    {
    	return $this->getPagerUrl(array($this->getLimitVarName()=>$limit));
    }
    
    public function getPagerUrl($params=array())
    {
    	$urlParams = array();
    	$urlParams['_current']  = true;
    	$urlParams['_escape']   = true;
    	$urlParams['_use_rewrite']   = true;
    	if($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->isCurrentlySecure()){
    		$urlParams['_secure']   = true;
    	}
    	$urlParams['_query']    = $params;
    	return $this->getUrl('*/*/*', $urlParams);
    }
	public function getRequest(){
		return $this->_objectManager->get('Magento\Framework\App\RequestInterface');
	}
	
}
