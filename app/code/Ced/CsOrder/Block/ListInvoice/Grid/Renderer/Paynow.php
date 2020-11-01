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
  * @category  Ced
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer;
class Paynow extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options
{
    protected $_objectManager;

    /**
     * Paynow constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Context $context,\Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {

        $this->_objectManager=$objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getPayNowButtonHtml($url = '')
    {
        return '<input class="button sacalable save" style="cursor: pointer; background: #ffac47 url("images/btn_bg.gif") repeat-x scroll 0 100%;border-color: #ed6502 #a04300 #a04300 #ed6502;    border-style: solid;    border-width: 1px;    color: #fff;    cursor: pointer;    font: bold 12px arial,helvetica,sans-serif;    padding: 1px 7px 2px;text-align: center !important; white-space: nowrap;" type="button" onclick="setLocation(\''.$url.'\')" value="PayNow">';
    }

    /**
     * @param string $url
     * @param string $label
     * @return string
     */
    protected function getRefundButtonHtml($url = '',$label = '')
    {
        return '<input class="button sacalable save" style="cursor: pointer; background: #ffac47 url("images/btn_bg.gif") repeat-x scroll 0 100%;border-color: #ed6502 #a04300 #a04300 #ed6502;    border-style: solid;    border-width: 1px;    color: #fff;    cursor: pointer;    font: bold 12px arial,helvetica,sans-serif;    padding: 1px 7px 2px;text-align: center !important; white-space: nowrap;" type="button" onclick="setLocation(\''.$url.'\')" value="RefundNow">';
    }
    
    protected function getPayNowButtonHtml123($url = '')
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label'     => __('PayNow'),
                    'onclick'   => "setLocation('".$url."')",
                    'class'     => 'save'
                    )
            )->toHtml();
    }
    
    protected function getRefundButtonHtml123($url = '',$label = '')
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(
                array(
                    'label'     => $label,
                    'onclick'   => "setLocation('".$url."')",
                    'class'     => 'go'
                    )
            )->toHtml();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        
        $html = "";
        foreach ($this->_getOptions() as $key => $value) {
            if($key == $row->getPaymentState()) {
                $html = $value->getText();
                break;
            }
        }
        if($row->getVendorId()!='') {
            if ($row->canPay()) {

                $url = $this->getUrl('csmarketplace/vpayments/new/', ['vendor_id' => $row->getVendorId(), 'order_ids' =>$row->getId(), 'type' => \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT ]);
                $html .="&nbsp;".$this->getPayNowButtonHtml($url);
            } elseif ($row->canRefund()) {
                $url =  $this->getUrl('csmarketplace/vpayments/new/', array('vendor_id' => $row->getVendorId(), 'order_ids'=>$row->getId(),'type' => \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT));
                $html = $this->getRefundButtonHtml($url, $html);
            }
        }
        return $html;   
    }
}
