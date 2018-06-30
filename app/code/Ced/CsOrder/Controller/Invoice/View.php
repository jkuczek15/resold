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
namespace Ced\CsOrder\Controller\Invoice; 
 
class View extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
* 
     *
 * @var \Magento\Framework\View\Result\Page 
*/
    protected $resultPageFactory;
    /**
* 
      * * @param \Magento\Framework\App\Action\Context $context      
*/
    /**
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
    
        $vendorId = $this->session->getVendorId();
        $register = $this->_objectManager->get('Magento\Framework\Registry');
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        /**
* 
         *
 * @var \Magento\Sales\Model\Order\Invoice $invoice 
*/
        $invoice = $this->_objectManager->create('Magento\Sales\Api\InvoiceRepositoryInterface')->get($invoiceId);
        $this->_objectManager->get("Ced\CsOrder\Model\Invoice")->setVendorId($vendorId)->updateTotal($invoice, true);
        if ($invoice) {
            $register->register('current_invoice', $invoice);
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Invoice').' # '.$invoice->getIncrementId());
            return $resultPage;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('csorder/invoice/');
        return $resultRedirect;
    }
}
 
