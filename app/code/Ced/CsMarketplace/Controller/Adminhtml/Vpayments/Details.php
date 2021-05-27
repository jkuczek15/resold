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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Adminhtml\Vpayments;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Details extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
     /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_coreRegistry = null;

 
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Customer edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function execute()
    {
        $rowId = $this->getRequest()->getParam('id');
        $row =  $this->_objectManager->get('Ced\CsMarketplace\Model\Vpayment')->load($rowId);
        if (!$row->getId()) {
            return $this->_redirect('*/*/', ['_secure' => true]);

        }

        $this->_coreRegistry->register('csmarketplace_current_transaction', $row);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_CsMarketplace::vendor_transaction');
        $resultPage->addBreadcrumb(__('CsMarketplace'), __('CsMarketplace'));
        $resultPage->addBreadcrumb(__('Manage Vendor Transactions'), __('Manage Vendor Transactions'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Vendor Transactions'));
        return $resultPage;


    }

}
