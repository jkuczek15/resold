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
class Vendorname extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_objectManager;

    /**
     * Vendorname constructor.
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
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $area=$this->_objectManager->get('Magento\Framework\View\DesignInterface')->getArea();
        if($row->getVendorId()!='') {          
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($row->getVendorId());
            $url = 'javascript:void(0);';
            $target = "";
            if ($area=='adminhtml') {
                $url =  $this->getUrl("csmarketplace/vendor/edit/", array('vendor_id' => $vendor->getId()));
                $target = "target='_blank'";
            }
            return "<a href='". $url . "' ".$target." >".$vendor->getName()."</a>";          
        }
         return '';
    }
}
