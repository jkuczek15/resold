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


namespace Ced\CsVendorReview\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
   
    protected $resultPageFactory;

    
    protected $resultPage;

    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {

        switch ($this->getRequest()->getControllerName()) {
            case 'review':
                return $this->reviewAcl();
            break;
            default:
                return $this->_authorization->isAllowed('Ced_CsMarketplace::csmarketplace');
            break;
        }
    }

    /**
     * ACL check for Review
     *
     * @return bool
     */
    protected function reviewAcl()
    {
        
        switch ($this->getRequest()->getActionName()) {
            default:
                return $this->_authorization->isAllowed('Ced_CsVendorReview::manage_review');
            break;
        }
    }
    public function execute()
    {
        
        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->setActiveMenu('Ced_Review::review');
        $this->resultPage ->getConfig()->getTitle()->set((__('Manage Vendor Review')));
        return $this->resultPage;
    }
}
