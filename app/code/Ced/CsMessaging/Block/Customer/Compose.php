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
class Compose extends \Magento\Framework\View\Element\Template
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
    protected $_messagingFactory;
    public $_objectManager;


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        array $data = [],
        Data $messagingHelper,
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
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
        $this->_objectManager = $objectInterface;
        $this->_messagingHelper = $messagingHelper;
        parent::__construct($context, $data);
    }

    public function getinboxcollection()
    {

        $receiver_name=$this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $vendor=$this->customerSession->getVendor();
        //print_r($vendor);die;
        $vendor_email=$vendor['email'];
        $collection = $this->_messagingFactory->create()->getCollection()->addFieldToFilter('receiver_email', $vendor_email)->addFieldToFilter('role', 'customer')->addFieldToFilter('postread', 'new')->setOrder('chat_id', 'desc')->getData();
        return $collection;

    }

    public function getCustomerCollection()
    {
        return $this->_objectManager->create('Magento\Customer\Model\Customer')->getCollection();

    }
    public function getVedndorEmail(){

    	return $this->customerSession->getVendor();
    }

}
