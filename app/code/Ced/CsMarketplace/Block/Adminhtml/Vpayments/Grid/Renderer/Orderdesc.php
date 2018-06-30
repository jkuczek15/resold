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
namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer;
class Orderdesc extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

	protected $_frontend = false;
	protected $_objectManager;
	protected $_currencyInterface;
	/**
	 * @param \Magento\Backend\Block\Context $context
	 * @param array $data
	 */
	public function __construct(\Magento\Backend\Block\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager, 
    	\Magento\Framework\Locale\Currency $localeCurrency,
		array $data = [])
	{
		$this->_objectManager=$objectManager;
   		$this->_currencyInterface = $localeCurrency;
		parent::__construct($context, $data);
	}
	
	public function render(\Magento\Framework\DataObject $row)
	{
		
		$amountDesc=$row->getAmountDesc();
		$html='';
		$area=$this->_objectManager->get('Magento\Framework\View\DesignInterface')->getArea();
		if($amountDesc!=''){
			$amountDesc=json_decode($amountDesc,true);
			foreach ($amountDesc as $incrementId=>$baseNetAmount){
					$url = 'javascript:void(0);';
					$target = "";
					$amount = $this->_currencyInterface->getCurrency($row->getBaseCurrency())->toCurrency($baseNetAmount);
					$vorder = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
					 
					$orderId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vorders')->load($incrementId,'order_id')->getId(); 
					
					if ($area!='adminhtml' && $vorder && $vorder->getId()) {
						$url =  $this->getUrl("csmarketplace/vorders/view/",array('order_id'=>$orderId));
						$target = "target='_blank'";
						$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'."<a href='". $url . "' ".$target." >".$incrementId."</a>".'</label>, <b>Net Earned </b>'.$amount.'<br/>';
					}
					else 
						$html .='<label for="order_id_'.$incrementId.'"><b>Order# </b>'.$incrementId.'</label>, <b>Amount </b>'.$amount.'<br/>';
			}
		}
		
		return $html;
	}
 
}