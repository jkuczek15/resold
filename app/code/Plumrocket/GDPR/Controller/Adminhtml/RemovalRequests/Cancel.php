<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Controller\Adminhtml\RemovalRequests;

use Plumrocket\GDPR\Model\RemovalRequestsFactory as RemovalFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory as RemovalResourceFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Plumrocket\GDPR\Model\Config\Source\RemovalStatus;
use Magento\Backend\Model\Auth\Session;

class Cancel extends \Plumrocket\Base\Controller\Adminhtml\Actions
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Plumrocket_GDPR::removalrequests_cancel';

    /**
     * @var RemovalFactory
     */
    protected $removalFactory;

    /**
     * @var RemovalResourceFactory
     */
    protected $removalResourceFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param RemovalFactory $removalFactory
     * @param RemovalResourceFactory $removalResourceFactory
     * @param DateTime $dateTime
     * @param Session $authSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        RemovalFactory $removalFactory,
        RemovalResourceFactory $removalResourceFactory,
        DateTime $dateTime,
        Session $authSession
    ) {
        parent::__construct($context);
        $this->removalFactory = $removalFactory;
        $this->removalResourceFactory = $removalResourceFactory;
        $this->dateTime = $dateTime;
        $this->authSession = $authSession;
    }

    /**
     * Cancel action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $request_id = $this->getRequest()->getParam('request_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($request_id) {
            try {
                // init model and delete
                /** @var \Plumrocket\GDPR\Model\removalRequests $removalRequest */
                $removalRequest = $this->removalFactory->create()->load($request_id);
                if ($removalRequest->getStatus() == RemovalStatus::PENDING) {
                    $removalRequest->addData([
                        'cancelled_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                        'cancelled_by' => $this->authSession->getUser()->getUserId(),
                        'scheduled_at' => null,
                        'status' => RemovalStatus::CANCELLED
                    ]);
                    $this->removalResourceFactory->create()->save($removalRequest);
                }
                // display success message
                $this->messageManager->addSuccessMessage(__('Account removal request has been canceled.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
            }
        }

        // display error message
        $this->messageManager->addErrorMessage(__('The request you trying to cancel cannot be found.'));

        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
