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

namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor;

class Edit extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * @param \Magento\Backend\App\Action\Context              $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date   $dateFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
    }

    
    /**
     * Initiate rule
     *
     * @return void
     */
    /*  protected function _initRule()
    {
        $this->_coreRegistry->register('vendor_data', $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor'));
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('vendor_id')) {
            $id = (int)$this->getRequest()->getParam('vendor_id');
        }
        if ($id) {
            $this->_coreRegistry->registry('vendor_data')->load($id);
        }
    } */

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Ced_CsMarketplace::csmarketplace')->_addBreadcrumb(__('Manage Vendor'), __('Vendor'));
        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_CsMarketplace::csmarketplace');
    }
    
    
    
    /**
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        
        $id = $this->getRequest()->getParam('vendor_id');
        $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Vendor no longer exists.'));
                $this->_redirect('csmarketplace/vendor/index');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        
        $this->_coreRegistry->register('vendor_data', $model);
        $this->_initAction();
        //    $this->_view->getLayout()->getBlock('vendor_entity_edit')->setData('action', $this->getUrl('csmarketplace/vendor/save'));

        $this->_addBreadcrumb($id ? __('Edit Vendor') : __('New Vendor'), $id ? __('Edit Vendor') : __('New Vendor'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Vendor')
        );

        $this->_view->renderLayout();
    }
}
