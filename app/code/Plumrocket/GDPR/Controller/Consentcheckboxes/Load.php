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

namespace Plumrocket\GDPR\Controller\Consentcheckboxes;

use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Element\Template;

/**
 * Load action.
 */
class Load extends Action
{
    /**
     * @var ResultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param ResultJsonFactory $resultJsonFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ResultJsonFactory $resultJsonFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $componentName = $this->getRequest()->getParam('componentName');

        $block = $this->resultPageFactory->create()->getLayout()
            ->createBlock(Template::class)
            ->setData('prgdprPage', ConsentLocations::NEWSLETTER)
            ->setData('componentName', $componentName)
            ->setTemplate('Plumrocket_GDPR::consent-checkboxes-xinit.phtml');

        $response = ['html' => $block->toHtml()];

        return $resultJson->setData($response);
    }
}
