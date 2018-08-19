<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Booking
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsMessaging\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;


class Massmessage extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory,Registry $registry)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {   
        $vendorIds = $this->getRequest()->getParam('vendor_id');
        $this->_registry->register('vendor_ids', $vendorIds);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Compose Message'));
        return $resultPage;
    }
}

