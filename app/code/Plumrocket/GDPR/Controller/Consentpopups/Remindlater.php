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

namespace Plumrocket\GDPR\Controller\Consentpopups;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;

/**
 * Remindlater action.
 */
class Remindlater extends Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Session $session
     * @param ResultJsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $session,
        ResultJsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->session = $session;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $this->session->setData('prgdpr_remindlater_notifys', true);

        $response = ['error' => false];

        return $resultJson->setData($response);
    }
}
