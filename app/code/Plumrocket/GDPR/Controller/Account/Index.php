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
 * Settings controller.
 */
class Index extends Action
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
     * Index constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param Session $session
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

        if (!$this->helper->moduleEnabled()) {
            $this->_forward('no_route');
        }

        return parent::dispatch($request);
    }

    /**
     * Execute controller.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('prgdpr_index')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->getPage()->getConfig()->getTitle()->set(__('Welcome to the Privacy Center'));
        $this->_view->renderLayout();
    }
}
