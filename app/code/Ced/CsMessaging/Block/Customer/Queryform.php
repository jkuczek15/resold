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
namespace Ced\CsMessaging\Block\Customer;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Ced\CsMessaging\Helper\Data;
/**
 * HTML anchor element block
 *
 * @method string getLabel()
 * @method string getPath()
 * @method string getTitle()
 */
class Queryform extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */


    /**
     * Prepare link attributes as serialized and formatted string
     *
     * @return string
     */

    const XML_PATH_TEMPLATE_ALLOW_SYMLINK = 'dev/template/allow_symlink';

    /**
     * Assigned variables for view
     *
     * @var array
     */

    protected $customerSession;

    protected $_viewVars = [];

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * JS URL
     *
     * @var string
     */
    protected $_jsUrl;

    public $_scopeConfig;

    /**
     * Is allowed symlinks flag
     *
     * @var bool
     */
    protected $_allowSymlinks;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template;

    /**
     * Template engine pool
     *
     * @var \Magento\Framework\View\TemplateEnginePool
     */
    protected $templateEnginePool;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Application state
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Root directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * Media directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    /**
     * Template context
     *
     * @var \Magento\Framework\View\Element\BlockInterface
     */
    protected $templateContext;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\View\Element\Template\File\Resolver
     */
    protected $resolver;

    /**
     * @var \Magento\Framework\View\Element\Template\File\Validator
     */
    protected $validator;
    public $_messagingFactory;
    public $_vendorFactory;
    public $_coreRegistry;


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        Data $messagingHelper,
        array $data = []
    ) {

        $this->_vendorFactory = $vendorFactory->create();
        $this->_messagingFactory = $messagingFactory;
        $this->customerSession = $customerSession;
        $this->validator = $context->getValidator();
        $this->resolver = $context->getResolver();
        $this->_filesystem = $context->getFilesystem();
        $this->templateEnginePool = $context->getEnginePool();
        $this->_storeManager = $context->getStoreManager();
        $this->_appState = $context->getAppState();
        $this->templateContext = $this;
        $this->pageConfig = $context->getPageConfig();
        $this->_coreRegistry = $registry;
        $this->_messagingHelper = $messagingHelper;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $data);

        $customerData = $this->customerSession->getCustomer();

          // $sender_id= $customerData->getId();
        $sender_email=$customerData->getEmail();

        $collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('role', 'customer')->addFieldToFilter('receiver_email', $sender_email)->setOrder('chat_id', 'desc');

        $this->setCollection($collection);
    }

     /* public function getinboxcollection()
     {

     	$receiver_name=$this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	$vendor=$this->customerSession->getVendor();
    	//print_r($vendor);die;
    	$vendor_email=$vendor['email'];

    	$collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('receiver_email',$vendor_email)->addFieldToFilter('role','customer')->addFieldToFilter('postread','new')->setOrder('chat_id','desc')->getData();
    	return $collection;

      } */

    /**
     * Serialize attributes
     *
     * @param  array $attributes
     * @param  string $valueSeparator
     * @param  string $fieldSeparator
     * @param  string $quote
     * @return string
     */


    public function getinboxcollection()
    {
        $customerData = $this->customerSession->getCustomer();
        $sender_email=$customerData->getEmail();
        $collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('role', 'customer')->addFieldToFilter('receiver_email', $sender_email)->setOrder('chat_id', 'desc')->getData();
        return $collection;
    }

    public function getCountCollection()
    {
    	$customerData = $this->customerSession->getCustomer();
    	$sender_email=$customerData->getEmail();
    	$collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('role', 'customer')->addFieldToFilter('receiver_email', $sender_email)->addFieldToFilter('postread', 'new')->setOrder('chat_id', 'desc')->getData();
    	return $collection;
    }

        /** get admin messages count collection **/
    public function getAdminMsgCountCollection()
    {
        $customerData = $this->customerSession->getCustomer();
        $receiver_email=$customerData->getEmail();
        $collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('role', 'admin')->addFieldToFilter('receiver_email', $receiver_email)->addFieldToFilter('postread', 'new')->addFieldToFilter('send_to', 'customer')->setOrder('chat_id', 'desc')->getData();
        return $collection;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

     protected function _prepareLayout()
    {
            parent::_prepareLayout();
            /** @var \Magento\Theme\Block\Html\Pager */
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'pager.identify.next.prev'
            );
            $pager->setLimit(5)
                ->setShowAmounts(false)
                ->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
            $this->getCollection()->load();

            return $this;
    }

    public function getVendorId()
    {
        if($this->_coreRegistry->registry('current_vendor'))
            return $this->_coreRegistry->registry('current_vendor')->getId();
    }

}
