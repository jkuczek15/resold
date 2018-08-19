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
namespace Ced\CsMessaging\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Read extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    
    public $_allowedResource = true;
    
    /**
 * @var Session 
*/
    protected $session;
    protected $_messagingFactory;
    
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
     
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory
    ) {
         $this->_messagingFactory = $messagingFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->urlModel = $urlFactory;
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_moduleManager = $moduleManager;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        //     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        
                $chatid=$this->getRequest()->getParam('id');
                $vendor_id=$this->session->getVendorId();
                $chat=$this->_messagingFactory->create()->load($chatid);
		        if($chat->getSenderId()!=$vendor_id) {
		            $chat->setData('postread', 'read');
		            $chat->save();
		        }
                $resultRedirect = $this->resultPageFactory->create();
                return $resultRedirect;
                
                
     
       
    }
    
    
    public function getVendorId()
    {
        return 'admin';
    }
}
