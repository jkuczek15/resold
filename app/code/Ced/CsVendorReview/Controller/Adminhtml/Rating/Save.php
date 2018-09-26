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


namespace Ced\CsVendorReview\Controller\Adminhtml\Rating;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action\Context;

class Save extends \Magento\Backend\App\Action
{
    protected $connection;
    protected $_resource;

    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_resource = $resource;
        parent::__construct($context);
    }
   
    public function execute()
    {
        
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating');
            $id = $this->getRequest()->getParam('id');
            if ($id) {       //Add Custom Rating Edit Validation
                                $model->load($id);
                if ($model->getRatingCode()==$data['rating_code']) {
                    $model->setData($data);
                    $model->save();
                                   $this->messageManager->addSuccess(__('Rating Criteria Has been Saved.'));
                } else {
                    $this->messageManager->addError(__('Sorry Rating Code Not Editable'));
                }
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } else {
                $collection=$model->getCollection()->addFieldToFilter('rating_code', $data['rating_code']);
                
                $model->setData($data);
                
                try {
                    if (count($collection) < 1) {
                        $this->addColumn($data['rating_code']);
                        $this->messageManager->addSuccess(__('Rating Criteria Has been Saved.'));
                        $model->save();
                        //Here we add a Custom DDL Cache Flush Code
                        $cacheManager = $this->_objectManager->create(\Magento\Framework\App\Cache\Manager::class);
                                                $cacheManager->clean([\Magento\Framework\DB\Adapter\DdlCache::TYPE_IDENTIFIER]);
                    } else {
                        $this->messageManager->addError(__('Rating Item with the same code already exists.'));
                    }
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                        return;
                    }
                    $this->_redirect('*/*/');
                    return;
                } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the rating.'));
                }
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
        $this->_redirect('*/*/');
    }
    public function addColumn($column)
    {
        try {
              $table=$this->_resource->getTableName('ced_csvendorreview_review');
            $query='ALTER TABLE '.$table.' ADD '.$column.' INT';
              $this->getConnection()->exec($query);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the rating.'));
        }
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        }
        return $this->connection;
    }
    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {

        switch ($this->getRequest()->getControllerName()) {
            case 'rating':
                return $this->ratingAcl();
            break;
            default:
                return $this->_authorization->isAllowed('Ced_CsMarketplace::csmarketplace');
            break;
        }
    }

    /**
     * ACL check for Rating
     *
     * @return bool
     */
    protected function ratingAcl()
    {
        
        switch ($this->getRequest()->getActionName()) {
            default:
                return $this->_authorization->isAllowed('Ced_CsVendorReview::manage_rating');
            break;
        }
    }
}
