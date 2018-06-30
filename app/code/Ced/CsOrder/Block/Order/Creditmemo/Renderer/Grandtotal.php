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
namespace Ced\CsOrder\Block\Order\Creditmemo\Renderer;

class Grandtotal extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     */
    public function render(\Magento\Framework\DataObject $row)
    {
    
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $vendorId = $objectManager->get('Magento\Customer\Model\Session')->getVendorId();
        $Creditmemo = $objectManager->get('Magento\Sales\Model\Order\Creditmemo')->load($row->getEntityId());
        $Creditmemo = $objectManager->get('Ced\CsOrder\Model\CreditmemoGrid')->setVendorId($vendorId)->updateTotal($Creditmemo);
        return $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($Creditmemo->getGrandTotal(),true,false);

    }
 
}
