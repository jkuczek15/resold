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

class AddComment extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoCommentSender
     */
    protected $creditmemoCommentSender;

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
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }

    /**
     * Add comment to creditmemo history
     *
     * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $creditmemoLoader=$this->_objectManager->create('Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader'); 
        $creditmemoCommentSender=$this->_objectManager->get('Magento\Sales\Model\Order\Email\Sender\CreditmemoCommentSender');
        $resultJsonFactory=$this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory'); 
        $resultRawFactory=$this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory'); 


        try {
            $this->getRequest()->setParam('creditmemo_id', $this->getRequest()->getParam('id'));
            $data = $this->getRequest()->getPost('comment');
            if (empty($data['comment'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a comment.')
                );
            }
            $creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
            $creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
            $creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
            $creditmemo = $creditmemoLoader->load();
            $comment = $creditmemo->addComment(
                $data['comment'],
                isset($data['is_customer_notified']),
                isset($data['is_visible_on_front'])
            );
            $comment->save();

            $creditmemoCommentSender->send($creditmemo, !empty($data['is_customer_notified']), $data['comment']);
            $onj=$this->_objectManager->get('Magento\Framework\View\Element\Context');
            $block=$onj->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Order\Creditmemo\View\Comments', 'creditmemo_comments');
            $shipmentBlock =$onj->getLayout()->createBlock('Magento\Sales\Block\Adminhtml\Order\Comments\View', 'order_comments')->setTemplate('order/comments/view.phtml');
            $block->append($shipmentBlock);
            $response = $block->toHtml(); 
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('Cannot add new comment.')];
        }
        if (is_array($response)) {
            $resultJson = $resultJsonFactory->create();
            $resultJson->setData($response);
            return $resultJson;
        } else {
            $resultRaw = $resultRawFactory->create();
            $resultRaw->setContents($response);
            return $resultRaw;
        }
    }
}
