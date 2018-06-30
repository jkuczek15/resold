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
namespace Ced\CsOrder\Controller\Adminhtml\Vendororder;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CommentsHistory
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddComment extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{

    /**
     * Generate order history for ajax request
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {   
        $order = $this->_initOrder();
         $resultJsonFactory=$this->_objectManager->get('Magento\Framework\Controller\Result\JsonFactory'); 
        $resultRawFactory=$this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory'); 

        if ($order) {
            try {
                $data = $this->getRequest()->getPost('history');
                if (empty($data['comment']) && $data['status'] == $order->getDataByKey('status')) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a comment.'));
                }

                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $history = $order->addStatusHistoryComment($data['comment'], $data['status']);
                $history->setIsVisibleOnFront($visible);
                $history->setIsCustomerNotified($notify);
                $history->save();

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                /**
* 
                 *
 * @var OrderCommentSender $orderCommentSender 
*/
                $orderCommentSender = $this->_objectManager
                    ->create('Magento\Sales\Model\Order\Email\Sender\OrderCommentSender');

                $orderCommentSender->send($order, $notify, $comment);
                $onj=$this->_objectManager->get('Magento\Framework\View\Element\Context');
                $block =$onj->getLayout()->createBlock('Ced\CsOrder\Block\Order\View\History', 'order_history')->setTemplate('order/view/history.phtml');
                $response = $block->toHtml(); 
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot add order history.')];
            }
            if (is_array($response)) {
                $resultJson = $resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }else {
                $resultRaw = $resultRawFactory->create();
                $resultRaw->setContents($response);
                return $resultRaw;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('csorder/*/'); 
      
    }
    
    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder()
    {
        $orderRepository=$this->_objectManager->get('Magento\Sales\Api\OrderRepositoryInterface');
        $coreRegistry=$this->_objectManager->get('Magento\Framework\Registry');
        $vorderId = $this->getRequest()->getParam('order_id');
        try {

            $order = $orderRepository->get($vorderId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        $coreRegistry->register('sales_order', $order);
        $coreRegistry->register('current_order', $order);
        return $order;
    }
   
}
