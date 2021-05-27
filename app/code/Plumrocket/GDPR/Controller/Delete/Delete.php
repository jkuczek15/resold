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

namespace Plumrocket\GDPR\Controller\Delete;

use Plumrocket\GDPR\Helper\Data;
use Plumrocket\GDPR\Model\EmailSender;
use Plumrocket\GDPR\Helper\CustomerData;
use Plumrocket\GDPR\Model\RemovalRequestsFactory as RemovalFactory;
use Plumrocket\GDPR\Model\ResourceModel\RemovalRequestsFactory as RemovalResourceFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Customer account delete action.
 */
class Delete extends Action
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var CustomerData
     */
    protected $customerData;

    /**
     * @var RemovalFactory
     */
    protected $removalFactory;

    /**
     * @var RemovalResourceFactory
     */
    protected $removalResourceFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param RemoteAddress $remoteAddress
     * @param DateTime $dateTime
     * @param Session $session
     * @param Data $helper
     * @param EmailSender $emailSender
     * @param CustomerData $customerData
     * @param RemovalFactory $removalFactory
     * @param RemovalResourceFactory $removalResourceFactory
     * @param JsonHelper $jsonHelper
     * @param ResultJsonFactory $resultJsonFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        RemoteAddress $remoteAddress,
        DateTime $dateTime,
        Session $session,
        Data $helper,
        EmailSender $emailSender,
        CustomerData $customerData,
        RemovalFactory $removalFactory,
        RemovalResourceFactory $removalResourceFactory,
        JsonHelper $jsonHelper,
        ResultJsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->formKeyValidator = $formKeyValidator;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
        $this->session = $session;
        $this->helper = $helper;
        $this->emailSender = $emailSender;
        $this->customerData = $customerData;
        $this->removalFactory = $removalFactory;
        $this->removalResourceFactory = $removalResourceFactory;
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Dispatch controller.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (!$this->helper->moduleEnabled() || !$this->helper->isAccountDeletionEnabled()) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\SessionException
     */
    public function execute()
    {
        $credentials = null;
        $httpBadRequestCode = 400;

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        try {
            $credentials = $this->jsonHelper->jsonDecode($this->getRequest()->getContent());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("Invalid Data!"));
            return $resultJson->setData(['ERROR!'])->setHttpResponseCode($httpBadRequestCode);
        }

        $this->getRequest()->setParam('form_key', $credentials['form_key']);
        $validFormKey = $this->formKeyValidator->validate($this->getRequest());
        if (!$credentials || !$validFormKey) {
            $this->messageManager->addErrorMessage(__("Invalid Data!"));
            return $resultJson->setData(['ERROR!'])->setHttpResponseCode($httpBadRequestCode);
        }

        $customerId = $this->session->getCustomerId();
        $currentCustomerDataObject = $this->customerData->getCustomerDataObject($customerId);

        try {
            $this->customerData->authenticate($currentCustomerDataObject, $credentials['password']);

            if ($this->customerData->hasOpenedOrders($customerId)) {
                $this->messageManager->addErrorMessage(
                    __("This account cannot be deleted because some orders are still pending. "
                        . "Please complete or cancel all orders before deleting your account.")
                );

                return $resultJson->setData(['ERROR!'])->setHttpResponseCode($httpBadRequestCode);
            }
            
            /** @var \Plumrocket\GDPR\Model\removalRequests $removalRequest */
            $removalRequest = $this->removalFactory->create()
                ->setData([
                    'created_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                    'scheduled_at' => date(
                        'Y-m-d H:i:s',
                        $this->dateTime->gmtTimestamp() + $this->helper->getDeletionTime()
                    ),
                    'customer_id' => $customerId,
                    'customer_ip' => $this->remoteAddress->getRemoteAddress(),
                    'website_id' => $this->storeManager->getStore()->getWebsiteId()
                ]);

            $this->removalResourceFactory->create()->save($removalRequest);
            $this->emailSender->sendRemovalRequestNotification($this->session->getCustomer());
            $this->session->logout();
            $response = ['errors' => false];
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultJson->setData(['ERROR!'])->setHttpResponseCode(401);
        } catch (UserLockedException $e) {
            $this->session->logout();
            $this->session->start();
            $this->messageManager
                ->addErrorMessage(__('You did not sign in correctly or your account is temporarily disabled.'));
            return $resultJson->setData(['ERROR!'])->setHttpResponseCode(401);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong, please try again later!'));
            return $resultJson->setData(['ERROR!'])->setHttpResponseCode(500);
        }

        return $resultJson->setData($response);
    }
}
