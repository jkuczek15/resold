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

namespace Ced\CsOrder\Block\Order\View;

use Magento\Sales\Model\Order\Address;

/**
 * Order history block
 * Class Info
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->metadata = $metadata;
        $this->_objectManager = $objectManager;
        $this->_metadataElementFactory = $elementFactory;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context, $registry, $adminHelper, $groupRepository, $metadata, $elementFactory, $addressRenderer, $data);
    }

    /**
     * Customer service
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $metadata;

    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * Metadata element factory
     *
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $_metadataElementFactory;

    /**
     * @var Address\Renderer
     */
    protected $addressRenderer;


    
    /**
     * Get URL to edit the customer.
     *
     * @return string
     */
    public function getCustomerViewUrl()
    {
        return '';//$this->getUrl('', ['id' => $this->getOrder()->getCustomerId()]);
    }

    /**
     * Get order view URL.
     *
     * @param  int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {   
        $vendor_id = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
        $order=$this->_objectManager->get('Magento\Sales\Model\Order')->load($orderId);
        $vorder= $this->_objectManager->get('Ced\CsOrder\Model\Vorders')->getCollection()->addFieldToFilter('order_id', trim($order->getIncrementId()))->addFieldToFilter('vendor_id', trim($vendor_id));
        if(count($vorder->getFirstItem())!=0 && $vorder->getFirstItem()->getId()) {
            return $this->getUrl('csorder/vorders/view', ['order_id' => $vorder->getFirstItem()->getId()]); 
        }
        else {
            return $this->getUrl('csorder/vorders/index'); 
        }    
    }

    
    /**
     * Get link to edit order address page
     *
     * @param  \Magento\Sales\Model\Order\Address $address
     * @param  string                             $label
     * @return string
     */
    public function getAddressEditLink($address, $label = '')
    {
        if ($this->_authorization->isAllowed('Magento_Sales::actions_edit')) {
           
            return ''; //return '<a href="' . $url . '">' . $label . '</a>';
        }

        return '';
    }

}
