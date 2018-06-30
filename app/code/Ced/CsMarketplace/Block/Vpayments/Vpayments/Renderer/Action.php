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
namespace Ced\CsMarketplace\Block\Vpayments\Vpayments\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
	protected $_vproduct;
	public function __construct(\Magento\Backend\Block\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager, 
		array $data = []
	) {
		$this->_objectManager = $objectManager;
		parent::__construct ( $context, $data );
	}
	public function render(\Magento\Framework\DataObject $row) {
		$html ='';
		$html.= '<span class="number"><a class="btn btn-info btn-outline btn-circle" title="View" href="'.$this->getUrl("csmarketplace/vpayments/view", array("payment_id" => $row->getId())).'"><i style="font-size:15px;" class="fa fa-info"></i></a></span>';
		return $html;
	}
	
}
