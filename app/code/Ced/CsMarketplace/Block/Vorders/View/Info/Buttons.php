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

namespace Ced\CsMarketplace\Block\Vorders\View\Info;

use Magento\Framework\Registry;

class Buttons extends \Magento\Framework\View\Element\Template
{
	public $_objectManager;

    public $_coreRegistry = null;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        Registry $registry
	)
    { 
		parent::__construct($context);
		$this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
	}
    protected function _construct()
    {
		parent::_construct();
        $this->setTemplate('vorders/view/info/buttons.phtml');
    }


    /**
     * Retrieve current order model instance
     * @return mixed
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Get url for printing order
     *
     * @param Mage_Sales_Order $order
     * @return string
     */
    public function getPrintUrl($vorder)
    {
        return $this->getUrl('csmarketplace/vorders/print', array('order_id' => $vorder->getId(),'_secure'=>true,'_nosid'=>true));
    }

    
}
