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
 * @category  Ced;
 * @package   Ced_CsMessaging
 * @author    CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMessaging\Block;

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
class Index extends \Magento\Framework\View\Element\Template
{

    protected $_viewVars = [];
    public $_scopeConfig;
    protected $_messagingFactory;
    public $_storeManager;


    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        Data $messagingHelper,
        array $data = []
    ) {

        $this->_messagingFactory = $messagingFactory;
        $this->customerSession = $customerSession;
        $this->_messagingHelper = $messagingHelper;
        parent::__construct($context, $data);

        $vendor=$this->customerSession->getVendor();
        $vendorEmail=$vendor['email'];

        $collection = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('sender_id', '0')
            ->addFieldToFilter('receiver_email', $vendorEmail)
            ->addFieldToFilter('send_to', 'vendor')
            ->setOrder('chat_id', 'desc');
        $this->setCollection($collection);

    }

     protected function _prepareLayout()
    {
            parent::_prepareLayout();
            /** @var \Magento\Theme\Block\Html\Pager */
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'pager.identify.next'
            );

            $limit=5;
            $pager->setLimit($limit)
                ->setShowAmounts(false)
                ->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
            $this->getCollection()->load();

            return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

}
