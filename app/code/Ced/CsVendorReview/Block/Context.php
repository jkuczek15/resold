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
 * @package   Ced_CsVendorReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsVendorReview\Block;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\ConfigInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract product block context
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Framework\View\Element\Template\Context
{
    /**
     * @var \Ced\CsVendorReview\Helper\Data
     */
    protected $_vendorReviewHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * @var \Ced\CsVendorReview\Model\Config
     */
    protected $_config;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;
    
    
    public function __construct(
        RequestInterface $request,
        LayoutInterface $layout,
        ManagerInterface $eventManager,
        UrlInterface $urlBuilder,
        CacheInterface $cache,
        DesignInterface $design,
        SessionManagerInterface $session,
        SidResolverInterface $sidResolver,
        ScopeConfigInterface $scopeConfig,
        Repository $assetRepo,
        ConfigInterface $viewConfig,
        StateInterface $cacheState,
        LoggerInterface $logger,
        Escaper $escaper,
        FilterManager $filterManager,
        TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\View\TemplateEnginePool $enginePool,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\Element\Template\File\Resolver $resolver,
        \Magento\Framework\View\Element\Template\File\Validator $validator,
        \Ced\CsVendorReview\Helper\Data $vendorReviewHelper,
        \Magento\Framework\Registry $registry,
        \Ced\CsVendorReview\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlFactory $urlFactory
    ) {
        $this->_vendorReviewHelper = $vendorReviewHelper;
        $this->registry = $registry;
        $this->_config = $config;
        $this->_objectManager=$objectManager;
        $this->_urlFactory=$urlFactory;
        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $urlBuilder,
            $cache,
            $design,
            $session,
            $sidResolver,
            $scopeConfig,
            $assetRepo,
            $viewConfig,
            $cacheState,
            $logger,
            $escaper,
            $filterManager,
            $localeDate,
            $inlineTranslation,
            $filesystem,
            $viewFileSystem,
            $enginePool,
            $appState,
            $storeManager,
            $pageConfig,
            $resolver,
            $validator
        );
    }

    /**
     * Function for getting developer helper object
     *
     * @return \Ced\CsVendorReview\Helper\Data
     */
    public function getCsVendorReviewHelper()
    {
        return $this->_vendorReviewHelper;
    }


    /**
     * Function for getting registry object
     *
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }
    
    /**
     * Function for getting csvendorreview model config object
     *
     * @return \Ced\CsVendorReview\Model\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }
    
    /**
     * Function for getting object manager object
     *
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }
    
    /**
     * Function for getting UrlFactory object
     *
     * @return \Magento\Framework\UrlFactory
     */
    public function getUrlFactory()
    {
        return $this->_urlFactory;
    }
}
