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
namespace Ced\CsMessaging\Block\Adminhtml\Message\Inbox;

use Magento\Framework\Filesystem;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\ObjectManagerInterface;
use Ced\CsMessaging\Model\Messaging;
use Ced\CsMarketplace\Model\VendorFactory;
use Ced\CsMessaging\Model\MessagingFactory;

/**
 * Class NewMessage
 * @package Ced\CsMessaging\Block\Adminhtml\Sent
 */
class View extends Template
{


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
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Root directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directory;


    /**
     * @var $messaging
     */
    protected $messaging;

    /**
     * @var ObjectManagerInterface
     */
    public $_objectManager;


    /**
     * NewMessage constructor.
     * @param Context $context
     * @param Messaging $messaging
     * @param array $data
     * @param ObjectManagerInterface $objectInterface
     */
    public function __construct(
        Context $context,
        Messaging $messaging,
        array $data = [],
        ObjectManagerInterface $objectInterface,
        VendorFactory $vendorFactory,
        MessagingFactory $messagingFactory
    ) {
        $this->_messaging = $messaging;
        $this->_vendorFactory = $vendorFactory->create();
        $this->_storeManager = $context->getStoreManager();
        $this->_objectManager = $objectInterface;
        $this->_messagingFactory = $messagingFactory;
        parent::__construct($context, $data);
    }


}