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

namespace Plumrocket\GDPR\Controller\Cookiesnotices;

use Magento\Cookie\Block\Html\Notices as NoticesBlock;

/**
 * Load action.
 */
class Load extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * Load constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
        $this->geoLocationHelper = $geoLocationHelper;
    }

    /**
     * Execute controller.
     */
    public function execute()
    {
        $response = ['html' => ''];
        $resultJson = $this->resultJsonFactory->create();

        if ($this->dataHelper->isCookieRestrictionModeEnabled()
            && $this->geoLocationHelper->isPassCookieGeoIPRestriction()
        ) {
            $resultPage = $this->resultPageFactory->create();
            /** @var NoticesBlock $block */
            $block = $resultPage->getLayout()
                ->createBlock(NoticesBlock::class)
                ->setTemplate('Plumrocket_GDPR::cookies/notices.phtml')
                ->setData('cookie_notice_text', $this->dataHelper->getCookieNoticeText())
                ->setData('cookie_accept_button_label', $this->dataHelper->getAcceptCookieButtonLabel())
                ->setData('cookie_decline_button_label', $this->dataHelper->getDeclineCookieButtonLabel());

            $response = ['html' => $block->toHtml()];
        }

        return $resultJson->setData($response);
    }
}
