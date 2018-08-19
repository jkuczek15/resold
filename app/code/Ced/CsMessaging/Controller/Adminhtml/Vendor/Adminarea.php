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
 * @package   Ced_CsMessaging
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMessaging\Controller\Adminhtml\Vendor;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Adminarea extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_messagingFactory = $messagingFactory;
    }
 
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        
        /**
 * @var \Magento\Backend\Model\View\Result\Page $resultPage 
*/
        $sender_id=$this->getRequest()->getParam('sender_id');
        if($this->getRequest()->getParam('sender_id')) {
            $sender_collection=    $this->_messagingFactory->create()->getCollection()->addFieldToFilter('sender_id', $sender_id)->getData();
            //$sender_collection=Mage::getModel('csvendorchat/chat')->getCollection()->addFieldToFilter('sender_id',$sender_id)->getData();
            foreach ($sender_collection as $key=>$value){
                $chat_id=$value['chat_id'];
          
                $chat=$this->_messagingFactory->create()->load($chat_id);
                // $chat=Mage::getModel("csvendorchat/chat")->load($chat_id);
        
                $chat->setData('postread', 'read');
                $chat->save();
            }
    
        }
        
        $resultRedirect = $this->resultPageFactory->create();
        return $resultRedirect;
       
    }
}