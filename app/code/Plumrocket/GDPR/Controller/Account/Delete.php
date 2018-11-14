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

namespace Plumrocket\GDPR\Controller\Account;

use Plumrocket\GDPR\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;

/**
 * Delete controller.
 */
class Delete extends Action
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Session $session
     * @param AccountData $accountData
     */
    public function __construct(Context $context, Data $helper, Session $session)
    {
        parent::__construct($context);

        $this->helper = $helper;
        $this->session = $session;
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
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('prgdpr_delete')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        if ($blockLink = $this->_view->getLayout()->getBlock('customer-account-navigation-prgdpr-link')) {
            $blockLink->setData('is_highlighted', true);
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Delete Your Account'));

        $this->_view->renderLayout();
    }
}
