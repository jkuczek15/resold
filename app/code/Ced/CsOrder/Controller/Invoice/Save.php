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
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
* 
     *
 * @var \Magento\Framework\View\Result\Page 
*/
    protected $resultPageFactory;
    
    protected $invoiceSender;

    /**
     * Save constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Framework\Registry $registry
     * @param ShipmentFactory $shipmentFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\Registry $registry,
        ShipmentFactory $shipmentFactory
    ) {
       
        $this->invoiceSender = $invoiceSender;
        $this->registry = $registry;
        $this->shipmentFactory = $shipmentFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->invoiceService = $invoiceService;
        


    }
    
    
    /**
     * Prepare shipment
     *
     * @param  \Magento\Sales\Model\Order\Invoice $invoice
     * @return \Magento\Sales\Model\Order\Shipment|false
     */
    protected function _prepareShipment($invoice)
    {

        $invoiceData = $this->getRequest()->getParam('invoice');

        $shipment = $this->shipmentFactory->create(
            $invoice->getOrder(),
            isset($invoiceData['items']) ? $invoiceData['items'] : [],
            $this->getRequest()->getPost('tracking')
        );

        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }

    /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     *
     * @return \Magento\Framework\Controller\ResultInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        /**
* 
         *
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultRedirectFactory->create();
        
        
        //$formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $formKeyIsValid = true;
        $isPost = $this->getRequest()->isPost();

        if (!$formKeyIsValid || !$isPost) {

            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            return $resultRedirect->setPath('csorder/vorders/index');
        }

        $data = $this->getRequest()->getPost('invoice');
        $vorderId = $this->getRequest()->getParam('order_id');
        $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($vorderId);
        
        $orderId = $vorder->getOrder()->getId();

        if (!empty($data['comment_text'])) {
            $this->_objectManager->get('Magento\Backend\Model\Session')->setCommentText($data['comment_text']);
        }

        try {
            $invoiceData = $this->getRequest()->getParam('invoice', []);
            
            $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
            /**
* 
             *
 * @var \Magento\Sales\Model\Order $order 
*/
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
            }

            if (!$order->canInvoice()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            if (!$invoice) {
                throw new LocalizedException(__('We can\'t save the invoice right now.'));
            }

            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $this->registry->register('current_invoice', $invoice);
            if (!empty($data['capture_case'])) {
                $invoice->setRequestedCaptureCase($data['capture_case']);
            }

            if (!empty($data['comment_text'])) {
                $invoice->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $invoice->setCustomerNote($data['comment_text']);
                $invoice->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }

            $invoice->register();

            $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = $this->_objectManager->create(
                'Magento\Framework\DB\Transaction'
            )->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $shipment = false;
            if (!empty($data['do_shipment']) || (int)$invoice->getOrder()->getForcedShipmentWithInvoice()) {
                $shipment = $this->_prepareShipment($invoice);
                if ($shipment) {
                    $transactionSave->addObject($shipment);
                }
            }
            
            $transactionSave->save();

            if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                $this->messageManager->addError(
                    __(
                        'The invoice and the shipment  have been created. ' .
                        'The shipping label cannot be created now.'
                    )
                );
            } elseif (!empty($data['do_shipment'])) {
                $this->messageManager->addSuccess(__('You created the invoice and shipment.'));
            } else {
                $this->messageManager->addSuccess(__('The invoice has been created.'));
            }

            // send invoice/shipment emails
            try {
                if (!empty($data['send_email'])) {
                    $this->invoiceSender->send($invoice);
                }
            } catch (\Exception $e) {

                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
            }
            if ($shipment) {
                try {
                    if (!empty($data['send_email'])) {
                        $this->shipmentSender->send($shipment);
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                    $this->messageManager->addError(__('We can\'t send the shipment right now.'));
                }
            }
            $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            return $resultRedirect->setPath('csorder/vorders/view', ['order_id' => $vorderId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        return $resultRedirect->setPath('csorder/*/new', ['order_id' => $vorderId]);
    }
}
 
