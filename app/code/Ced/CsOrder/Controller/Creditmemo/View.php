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
namespace Ced\CsOrder\Controller\Creditmemo; 
 
class View extends \Ced\CsMarketplace\Controller\Vendor
{
      /**
     * Creditmemo information page
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {   
        $coreRegistry=$this->_objectManager->get('Magento\Framework\Registry');
        $Creditmemo_id=$this->getRequest()->getParam('creditmemo_id');
        $vendorId = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
        if ($Creditmemo_id) {
            $Creditmemo=$this->_objectManager->get('Magento\Sales\Model\Order\Creditmemo')->load($Creditmemo_id);
            $this->_objectManager->get('Ced\CsOrder\Model\Creditmemo')->setVendorId($vendorId)->updateTotal($Creditmemo);
            $coreRegistry->register('current_creditmemo', $Creditmemo, true);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Creditmemo').' # '.$Creditmemo->getIncrementId());
            return $resultPage;            
        }else{
            return $this->messageManager->addError(__('Creditmemo Does not exists.'));
        }   
    }
}
 
