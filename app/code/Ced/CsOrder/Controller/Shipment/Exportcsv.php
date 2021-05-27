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
namespace Ced\CsOrder\Controller\Shipment;
 
class Exportcsv extends \Ced\CsMarketplace\Controller\Vendor
{
    
    /**
     * Grid action
     *
     * @return void
     */
    public function execute()
    {
        $fileName   = 'creditmemos.csv';
        $grid       = $this->resultPageFactory->create(true)->getLayout()->createBlock('Ced\CsOrder\Block\ListShipment\Grid');
        $obj=$this->_objectManager->get('Magento\Core\Controller\Varien\Action');
        $obj->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
}
