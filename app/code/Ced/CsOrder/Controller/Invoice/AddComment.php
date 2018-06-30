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

use Magento\Framework\Exception\LocalizedException;

class AddComment extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var invoiceCommentSender
     */
    protected $invoiceCommentSender;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pagePageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    protected $session;


    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_invoice');
    }

    /**
     * Add comment to creditmemo history
     *
     * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $invoiceCommentSender=$this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\InvoiceCommentSender');
        $resultJsonFactory=$this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory'); 
        $resultRawFactory=$this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory'); 
        $resultForwardFactory=$this->_objectManager->get('Magento\Backend\Model\View\Result\ForwardFactory'); 
        $resultPageFactory=$this->_objectManager->get('Magento\Framework\View\Result\PageFactory');
        try {
            $this->getRequest()->setParam('invoice_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a comment.'));
            }
            $invoice=$this->_objectManager->get('Magento\Sales\Model\Order\Invoice')->load($this->getRequest()->getParam('invoice_id'));
            if (!$invoice) {
                /**
                 * 
                 *
                 * @var \Magento\Backend\Model\View\Result\Forward $resultForward 
                 */
                $resultForward = $resultForwardFactory->create();
                return $resultForward->forward('noroute');
            }
            $invoice->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );

            $invoiceCommentSender->send($invoice, !empty($data['is_customer_notified']), $data['comment']);
            $invoice->save();
            $coreRegistry=$this->_objectManager->get('Magento\Framework\Registry');
            $coreRegistry->register('current_invoice', $invoice, true);
           
            /**
             * 
             *
             * @var \Magento\Backend\Model\View\Result\Page $resultPage 
             */
            $resultPage = $resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Invoices'));
            $onj=$this->_objectManager->get('Magento\Framework\View\Element\Context');
            $block=$onj->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Order\Invoice\View\Comments', 'invoice_comments');
            $shipmentBlock =$onj->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Order\Comments\View', 'order_comments')->setTemplate('order/comments/view.phtml');
            $block->append($shipmentBlock);
            $response = $shipmentBlock->toHtml(); 
        } catch (LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Please enter a comment.')];
        }
        if (is_array($response)) {
            /**
             * 
             *
             * @var \Magento\Framework\Controller\Result\Json $resultJson 
             */
            $resultJson = $resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        } else {
            /**
             * 
             *
             * @var \Magento\Framework\Controller\Result\Raw $resultRaw 
             */
            $resultRaw = $resultRawFactory->create();
            $resultRaw->setContents($response);
            return $resultRaw;
        }
    }
}
