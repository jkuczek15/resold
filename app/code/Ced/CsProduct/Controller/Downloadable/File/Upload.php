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
 * @category    Ced
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProduct\Controller\Downloadable\File;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
use Magento\Framework\Controller\ResultFactory;

class Upload extends \Ced\CsProduct\Controller\Vproducts
{
   /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_productBuilder;
    protected $urlBuilder;
    protected $_customerSession;
    protected $_scopeConfig;
    protected $_storeManager;

    protected $_link;
    protected $_sample;
    protected $_fileHelper;
    private $uploaderFactory;
    private $storageDatabase;
    

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        Session $customerSession,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
         UrlFactory $urlFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Downloadable\Model\Link $link,
        \Magento\Downloadable\Model\Sample $sample,
        \Ced\CsProduct\Helper\File $fileHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\MediaStorage\Helper\File\Storage\Database $storageDatabase
    ) {
        $this->stockFilter = $stockFilter;
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
        $this->_productBuilder = $productBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_session = $context->getSession();
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->urlBuilder = $this->_url;

        $this->_link = $link;
        $this->_sample = $sample;
        $this->_fileHelper = $fileHelper;
        $this->uploaderFactory = $uploaderFactory;
        $this->storageDatabase = $storageDatabase;
    }

    /**
     * @return mixed
     */
    
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        
        $tmpPath = '';
        if ($type == 'samples') {
            $tmpPath = $this->_sample->getBaseTmpPath();
            $extformat = $this->_scopeConfig->getValue('ced_vproducts/downloadable_config/sample_formats',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } elseif ($type == 'links') {
            $tmpPath = $this->_link->getBaseTmpPath();
            $extformat = $this->_scopeConfig->getValue('ced_vproducts/downloadable_config/link_formats',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        } elseif ($type == 'link_samples') {
            $tmpPath = $this->_link->getBaseSampleTmpPath();
            $extformat = $this->_scopeConfig->getValue('ced_vproducts/downloadable_config/link_formats',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        $extformat = explode(',',$extformat);
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $type]);

            $result = $this->_fileHelper->uploadFromTmp($tmpPath, $uploader,$extformat);

            if (!$result) {
                throw new \Exception('File can not be moved from temporary folder to the destination folder.');
            }

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
            
            if(isset($result['path']))
            $result['path'] = str_replace('\\', '/', $result['path']);

           
            if (isset($result['file'])) {
                $relativePath = rtrim($tmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->storageDatabase->saveFile($relativePath);
            }

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
