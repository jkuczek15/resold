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

class Lists extends \Magento\Framework\View\Element\Template
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
        $reviews = $this->_objectManager->create('Ced\CsVendorReview\Model\Review')->getCollection()
            ->addFieldToFilter('vendor_id', $this->getVendorId())
            ->addFieldToFilter('status', 1)
            ->setOrder('created_at', 'desc');
        $this->setReviews($reviews);
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $toolbar = $this->getLayout()->getBlock('product_review_list.toolbar');
        if ($toolbar) {
            $toolbar->setCollection($this->getReviews());
            $this->setChild('toolbar', $toolbar);
            $this->getReviews()->load();
        }
        return $this;
    }
    public function getRatings()
    {
        $rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')->getCollection()
            ->setOrder('sort_order', 'asc');
        return $rating;
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
}
