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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block;

use Magento\Framework\App\ObjectManager;

/**
 * Google Tag Manager Page Block
 */
class Gtm extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    private $cookieHelper;

    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $helper;

    /**
     * @todo we need to specified $cookieHelper argument as a Proxy Class
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\GDPR\Helper\Data $helper
     * @param \Magento\Cookie\Helper\Cookie|null $cookieHelper
     * @param array $data
     */
    public function __construct(// @codingStandardsIgnoreLine we need getting CookieHelper dynamically
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\GDPR\Helper\Data $helper,
        \Magento\Cookie\Helper\Cookie $cookieHelper = null, // @codingStandardsIgnoreLine see docs
        array $data = []
    ) {
        $this->cookieHelper = $cookieHelper ?: ObjectManager::getInstance()->get(\Magento\Cookie\Helper\Cookie::class);
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Render GTM tracking scripts
     *
     * @return string
     */
    protected function _toHtml()// @codingStandardsIgnoreLine we need to extend parent method
    {
        if (!$this->helper->isGtmEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Return cookie restriction mode value.
     *
     * @return bool
     */
    public function isCookieRestrictionModeEnabled()
    {
        return $this->cookieHelper->isCookieRestrictionModeEnabled();
    }

    /**
     * Return current website id.
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentWebsiteId()
    {
        return $this->_storeManager->getWebsite()->getId();
    }
}
