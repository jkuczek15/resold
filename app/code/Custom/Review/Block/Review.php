<?php
/**
 * Copyright © 2016 Resold. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Custom\Review\Block;

use Ced\CsMessaging\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Backend\Block\Template\Context;

class Review extends \Magento\Framework\View\Element\Template
{
    /**
     * Sell block constructor.
     * @param Session $customerSession
     * @param Context $context
     * @param Data $messagingHelper
     * @param array $data
     */
    public function __construct(
        Session $customerSession,
        Context $context,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
       $this->pageConfig->getTitle()->set(__('Post-Sale Review'));
       return parent::_prepareLayout();
    }

    public function getVendorId()
    {
      return $this->customerSession->getVendorId();
    }
}
