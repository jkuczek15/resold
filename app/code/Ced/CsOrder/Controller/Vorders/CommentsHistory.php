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
namespace Ced\CsOrder\Controller\Vorders;

use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
/**
 * Class CommentsHistory
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CommentsHistory extends \Ced\CsMarketplace\Controller\Vendor
{

    /**
     * Generate order history for ajax request
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {  
        
        $layoutFactory=$this->_objectManager->get('Magento\Framework\View\LayoutFactory');
        $resultRawFactory= $this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory');
        $this->_initOrder();
        $layout = $layoutFactory->create();
        $html = $layout->createBlock('Magento\Sales\Block\Adminhtml\Order\View\Tab\History')
            ->toHtml();

        //$translateInline->processResponseBody($html);
        /**
 * @var \Magento\Framework\Controller\Result\Raw $resultRaw 
*/
        $resultRaw = $resultRawFactory->create();
        $resultRaw->setContents($html);
        return $resultRaw;
    }
    protected function _initOrder()
    {
        $orderRepository=$this->_objectManager->get('Magento\Sales\Api\OrderRepositoryInterface');
        $coreRegistry=$this->_objectManager->get('Magento\Framework\Registry');
        $vorderId = $this->getRequest()->getParam('order_id');
        $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->load($vorderId);
        $orderId = $vorder->getOrder()->getId();
        try {

            $order = $orderRepository->get($orderId);
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
