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

use Magento\Backend\App\Action\Context;

class MassDelete extends \Magento\Backend\App\Action
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
    public function execute()
    {
        
        $ids = $this->getRequest()->getParam('id');
        if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select rating(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->get('Ced\CsVendorReview\Model\Rating')->load($id);
                    /*$column=$row->getRatingCode();
                    $this->remColumn($column);*/
                    $row->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }
    /*public function remColumn($column)
    {
        $table=$this->_resource->getTableName('ced_csvendorreview_review');
    $query='ALTER TABLE '.$table.' DROP COLUMN '.$column;
        $this->getConnection()->exec($query);
    }
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        }
        return $this->connection;
    }*/
}
