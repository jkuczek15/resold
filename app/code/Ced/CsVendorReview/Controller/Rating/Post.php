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

use Magento\Framework\App\Action\Context;
use Ced\CsVendorReview\Model\Review;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $model;

    public function __construct(
        Context $context,
        Review $model,
        array $data = [])
    {
       $this->model=$model;
        parent::__construct($context);
    }
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
            if ($scopeConfig->getValue('ced_csmarketplace/vendorreview/vendorapprval')) {
                $msg = 'Your review has been submited for approval';
                $data['status'] = 0;
            } else {
                $msg = 'Your review has been submitted successfully';
                $data['status'] = 1;
            }

            $this->model->setData($data);
            try {
                 $this->model->save();
                 $this->messageManager->addSuccess(__($msg), 'message_manager_example');
                $this->_redirect('*/*/lists', ['id'=>$data['vendor_id']]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addSuccess(__('Something went wrong while submitting review'));
            }
        }
    }
}
