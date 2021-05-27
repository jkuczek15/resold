<?php
/**
 * Resold
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category    Resold
 * @package     Resold
 * @author      Resold Core Team <dev@resold.us>
 * @copyright   Copyright Resold (https://resold.us/)
 * @license     https://resold.us/license-agreement
 */
namespace Resold\Api\Controller\Messages;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    public $_messagingFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_productRepositoryInterface = $productRepositoryInterface;
        $this->_messagingFactory = $messagingFactory;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
      $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
      $connection = $resource->getConnection();

      $post = $this->getRequest()->getPostValue();
      if(empty($post) || !isset($post['chat_id'])){
        return $this->resultJsonFactory->create()->setData(['error' => 'You have sent an unsupported request type.']);
      }// end if post array empty

      // delete the message
      $chat_id = $post['chat_id'];
      $chat = $this->_messagingFactory->create()->load($chat_id);

      if($this->session->getId() == $chat->getSenderId()){
        $column = 'sent_box';
      }else{
        $column = 'inbox';
      }

      $sql = "UPDATE ced_csmessaging SET ".$column." = 0 WHERE chat_id = '".$chat_id."'";
      $connection->query($sql);

      $this->messageManager->addSuccess(__("Successfully deleted message."));
      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute
}
