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

namespace Plumrocket\GDPR\Controller\Export;

use Plumrocket\GDPR\Api\DataExportInterface;
use Plumrocket\GDPR\Helper\Data;
use Plumrocket\GDPR\Helper\CustomerData;
use Plumrocket\GDPR\Model\Account\Processor;
use Plumrocket\GDPR\Model\EmailSender;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\State\UserLockedException;
use Plumrocket\GDPR\Model\ExportLogFactory as LogFactory;
use Plumrocket\GDPR\Model\ResourceModel\ExportLogFactory as LogResourceFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Export customer data.
 */
class Export extends Action
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var CustomerData
     */
    protected $customerData;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AuthenticationInterface
     */
    protected $authentication;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * @var LogResourceFactory
     */
    protected $logResourceFactory;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Export constructor.
     *
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param Data $helper
     * @param EmailSender $emailSender
     * @param CustomerData $customerData
     * @param Processor $processor
     * @param CustomerRepositoryInterface $customerRepository
     * @param AuthenticationInterface $authentication
     * @param Session $session
     * @param LogFactory $logFactory
     * @param LogResourceFactory $logResourceFactory
     * @param RemoteAddress $remoteAddress
     * @param DateTime $dateTime
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        Data $helper,
        EmailSender $emailSender,
        CustomerData $customerData,
        Processor $processor,
        CustomerRepositoryInterface $customerRepository,
        AuthenticationInterface $authentication,
        Session $session,
        LogFactory $logFactory,
        LogResourceFactory $logResourceFactory,
        RemoteAddress $remoteAddress,
        DateTime $dateTime
    ) {
        parent::__construct($context);

        $this->context = $context;
        $this->formKeyValidator = $formKeyValidator;
        $this->helper = $helper;
        $this->emailSender = $emailSender;
        $this->customerData = $customerData;
        $this->processor = $processor;
        $this->customerRepository = $customerRepository;
        $this->authentication = $authentication;
        $this->session = $session;
        $this->logFactory = $logFactory;
        $this->logResourceFactory = $logResourceFactory;
        $this->remoteAddress = $remoteAddress;
        $this->dateTime = $dateTime;
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
        if (! $this->session->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        if (! $this->helper->moduleEnabled() || ! $this->helper->isAccountExportEnabled()) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute export action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $validFormKey = $this->formKeyValidator->validate($this->getRequest());

        if ($this->getRequest()->isPost() && ! $validFormKey) {
            return $resultRedirect->setPath('prgdpr/account/export');
        }

        $customerId = $this->session->getCustomerId();
        $currentCustomerDataObject = $this->customerData->getCustomerDataObject($customerId);

        try {
            $password = $this->getRequest()->getPost('password');
            $this->customerData->authenticate($currentCustomerDataObject, $password);
            $this->processor->exportData($currentCustomerDataObject);

            $exportLog = $this->logFactory->create()->setData([
                'created_at' => date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp()),
                'customer_id' => $customerId,
                'customer_ip' => $this->remoteAddress->getRemoteAddress()
            ]);
            $this->logResourceFactory->create()->save($exportLog);
            $this->emailSender->sendDownloadDataNotification($this->session->getCustomer());
            //$this->messageManager->addSuccessMessage(__('Your Data Will Begin Downloading Momentarily.'));
        } catch (InvalidEmailOrPasswordException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('prgdpr/account/export');
        } catch (UserLockedException $e) {
            $this->session->logout();
            $this->session->start();
            $this->messageManager
                ->addErrorMessage(__('You did not sign in correctly or your account is temporarily disabled.'));
            return $resultRedirect->setPath('prgdpr/account/export');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('prgdpr/account/export');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_RAW);
    }
}
