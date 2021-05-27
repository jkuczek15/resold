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


namespace Ced\CsVendorReview\Controller\Rating;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Lists extends \Magento\Framework\App\Action\Action
{

    protected $_coreRegistry;
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStore()->getId();
        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')
            ->setStoreId($storeId)->loadByAttribute('entity_id', $id);

        if (!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->canShow($vendor)) {
            return false;
        } elseif (!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->isShopEnabled($vendor)) {
            return false;
        }

        $this->_coreRegistry = $this->_objectManager->get('Magento\Framework\Registry');
        $this->_coreRegistry->register('current_vendor', $vendor);
        $resultPage = $this->_objectManager->create('\Magento\Framework\View\Result\PageFactory');

        $resultPage =  $resultPage->create();
        $resultPage->getConfig()->getTitle()->set(__('Post-Sale Review'));

        return $resultPage;
    }
}
