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

use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Helper\Notifys;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;

/**
 * Load action.
 */
class Load extends Action
{
    /**
     * @var Checkboxes
     */
    protected $checkboxes;

    /**
     * @var Notifys
     */
    protected $notifys;

    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Checkboxes $checkboxes
     * @param Notifys $notifys
     * @param ResultJsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Checkboxes $checkboxes,
        Notifys $notifys,
        ResultJsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory,
        Session $session
    ) {
        parent::__construct($context);

        $this->checkboxes = $checkboxes;
        $this->notifys = $notifys;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        $response = ['html' => ''];
        $resultJson = $this->resultJsonFactory->create();

        if ($this->session->getCustomerId()) {
            $resultPage = $this->resultPageFactory->create();
            $checkboxes = $this->checkboxes->getCheckboxes(ConsentLocations::REGISTRATION, null, false, false);

            $checkboxesPages = [];

            foreach ($checkboxes as $key => $checkbox) {
                if (! $checkbox['is_required']) {
                    unset($checkboxes[$key]);
                    continue;
                }

                if ($checkbox['page_id']) {
                    $checkboxesPages[] = $checkbox['page_id'];
                }
            }

            $notifys = $this->notifys->getNotifys($checkboxesPages);

            $block = $resultPage->getLayout()
                    ->createBlock(Template::class)
                    ->setData('popups', $checkboxes)
                    ->setData('notifys', $notifys)
                    ->setTemplate('Plumrocket_GDPR::consent-popups-xinit.phtml')
                    ->toHtml();
            
            $response = ['html' => $block];
        }

        return $resultJson->setData($response);
    }
}
