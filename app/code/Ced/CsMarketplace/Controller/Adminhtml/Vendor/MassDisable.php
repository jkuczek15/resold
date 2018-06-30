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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor;

class MassDisable extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry         $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }
        
    
    /**
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $inline = $this->getRequest()->getParam('inline', 0);
        $vendorIds = $this->getRequest()->getParam('vendor_id');
        $shop_disable = $this->getRequest()->getParam('shop_disable', '');
        if ($inline) {
            $vendorIds = array($vendorIds);
        }
        if (!is_array($vendorIds)) {
            $this->messageManager->addErrorMessage(__('Please select vendor(s)'));
        } else {
            try {
                $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vshop');
                $change = $model->saveShopStatus($vendorIds, $shop_disable);
                $this->messageManager->addSuccessMessage(__('Total of %1 shop(s) have been updated.', $change));  
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage().' An error occurred while updating the vendor(s) Shop status.'));
            }
        }
        $this->_redirect('*/*/index', array('_secure'=>true));                
    }
    
}
