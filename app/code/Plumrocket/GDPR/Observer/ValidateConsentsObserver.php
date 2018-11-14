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

namespace Plumrocket\GDPR\Observer;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * Class ValidateConsentsObserver
 */
class ValidateConsentsObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    private $actionFlag;

    /**
     * @var \Plumrocket\GDPR\Helper\Checkboxes
     */
    private $checkboxesHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * ValidateCheckboxesObserver constructor.
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Plumrocket\GDPR\Helper\Checkboxes $checkboxesHelper,
        \Plumrocket\GDPR\Helper\Data $dataHelper
    ) {
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->checkboxesHelper = $checkboxesHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var null|\Magento\Framework\App\RequestInterface $request */
        $request = $observer->getData('request');
        /** @var null|\Magento\Framework\App\Action\AbstractAction $controllerAction */
        $controllerAction = $observer->getData('controller_action');

        if ($this->dataHelper->moduleEnabled()
            && $request
            && $controllerAction
        ) {
            $this->validateConsents($request, $controllerAction);
        }
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\Action\AbstractAction $controllerAction
     * @return $this
     */
    private function validateConsents($request, $controllerAction)
    {
        /** @var array $consents */
        $consents = $request->getParam('consent');
        $location = null;
        $errorMessage = __('Please provide your consent to all terms.');

        switch ($request->getModuleName()) {
            case 'newsletter':
                $location = ConsentLocations::NEWSLETTER;
                $errorMessage = __('Please provide your consent to all terms before subscribing to our newsletter.');
                break;
            case 'contact':
                $location = ConsentLocations::CONTACT_US;
                $errorMessage = __('Please provide your consent to all terms before submitting this form.');
                break;
        }

        $isValidRequest = true;

        if ($this->checkboxesHelper->isValidLocation($location)) {
            if (!$this->checkboxesHelper->isValidConsents($consents, $location)) {
                $isValidRequest = false;
                $this->messageManager->addErrorMessage($errorMessage);
            }
        } else {
            $isValidRequest = false;
            $this->messageManager->addErrorMessage(
                __('Undefined location for specified consents.')
            );
        }

        if (!$isValidRequest) {
            $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
            $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_POST_DISPATCH, true);

            $controllerAction->getResponse()->setRedirect(
                $this->redirect->getRedirectUrl()
            );
        }

        return $this;
    }
}
