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

namespace Ced\CsMarketplace\Block\Vpayments;

class Vpayments extends \Magento\Backend\Block\Widget\Container
{    
	protected $_template = 'vpayments/vpayments/list.phtml';
	
	protected function _construct()
    {
        $this->_controller = 'vpayments_listblock';
        $this->_blockGroup = 'Ced_CsMarketplace';
        $this->_headerText = __('Request Transaction List');
        parent::_construct();
        $this->removeButton('add');
      
    }
    
    
    protected function _prepareLayout()
    {
    	$this->setChild(
    			'transaction_grid',
    			$this->getLayout()->createBlock('Ced\CsMarketplace\Block\Vpayments\Vpayments\Grid', 'marketplace.transaction.grid')
    	);
    	return parent::_prepareLayout();
    }
    
    public function getGridHtml()
    {
    	return $this->getChildHtml('transaction_grid');
    }
}
