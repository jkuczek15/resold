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

namespace Ced\CsVendorReview\Block\Rating;

use Magento\Framework\Registry;

class Form extends \Magento\Framework\View\Element\Template
{
    protected $_vendor;
    
    protected $_storeManager;
    
    public $_objectManager;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_objectManager=$objectManager;
        $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
        
        $vendor_id = $this->getVendor()->getCustomerId();
        $customer = $customerSession->getCustomer();
        if ($customer && $customer->getId()) {
            $this->setCustomername($customer->getFirstname());
            $this->setCustomerid($customer->getId());
        }
        
        $this->setAllowWriteReviewFlag($customerSession->isLoggedIn());
        $this->setCustomerIsVendor($customerSession->isLoggedIn() && $customer->getId() == $vendor_id);
        $this->setLoginLink($this->getUrl('customer/account/login/'));
    }
    
    public function getVendor()
    {
        if (!$this->_vendor) {
            $this->_vendor=$this->_coreRegistry->registry('current_vendor');
        }
        return $this->_vendor;
    }
    public function getVendorId()
    {
        return $this->getVendor()->getId();
    }
    
    public function getRatingOption()
    {
        return [
        '0'        => __('Please Select Option'),
        '20'    => __('1 OUT OF 5'),
        '40'    => __('2 OUT OF 5'),
        '60'    => __('3 OUT OF 5'),
        '80'    => __('4 OUT OF 5'),
        '100'    => __('5 OUT OF 5')
        ];
    }
    public function getRatings()
    {
        $rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')->getCollection();
        return $rating;
    }
    public function getAction()
    {
        return $this->getUrl('csvendorreview/rating/post');
    }
}
