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
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Sales\Model\Service\InvoiceService;

class NewAction extends \Ced\CsMarketplace\Controller\Vendor
{
   
    protected $_objectManager;
    private $invoiceService;
    private $registry;
    protected $creditmemoLoader;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param InvoiceService $invoiceService
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        InvoiceService $invoiceService,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->creditmemoLoader = $creditmemoLoader;
        $this->resultPageFactory=$resultPageFactory;    
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->invoiceService = $invoiceService;

    }
    

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {   
    
        $csOrderHelper = $this->_objectManager->get('Ced\CsOrder\Helper\Data');

        try{
            $vendorId = $this->session->getVendorId();
            $vorderId = $this->getRequest()->getParam('order_id');
            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($vorderId);

            if(!$csOrderHelper->canCreateCreditmemoEnabled($vorder)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Not allowed to create Credit Memo.'));
            }
            $this->creditmemoLoader->setOrderId($vorder->getOrder()->getId());
              $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
              $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
              $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
              $creditmemo = $this->creditmemoLoader->load();
              $this->_objectManager->get('Ced\CsOrder\Model\Creditmemo')->setVendorId($vendorId)->updateTotal($creditmemo);//update Invoice total

            if ($creditmemo) {
                if ($comment = $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true)) {
                    $creditmemo->setCommentText($comment);
                }
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->prepend(__('Credit Memos'));
                if ($creditmemo->getInvoice()) {
                    $resultPage->getConfig()->getTitle()->prepend(
                        __("New Memo for #%1", $creditmemo->getInvoice()->getIncrementId())
                    );
                } else {
                    $resultPage->getConfig()->getTitle()->prepend(__("New Memo"));
                }
                return $resultPage;
            } else {
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        }catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addError($exception->getMessage());
            return $this->_redirectToOrder($vorderId);
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception, 'Cannot create an Shipment.');
            return $this->_redirectToOrder($vorderId);
        }
        
    }
    
    /**
     * Redirect to order view page
     *
     * @param  int $orderId
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function _redirectToOrder($orderId)
    {
        /**
* 
         *
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('csorder/vorders/view', ['order_id' => $orderId]);
        return $resultRedirect;
    }
}
 
