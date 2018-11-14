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

namespace Plumrocket\GDPR\Helper;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Cookie\Helper\Cookie;
use Magento\Store\Model\StoreManagerInterface;

class Data extends Main
{
    const SECTION_ID = 'prgdpr';
    const LAST_ALLOW_COOKIE_NAME = 'allowed_cookies_datetime';
    const LAST_DECLINE_COOKIE_NAME = 'declined_cookies_datetime';
    const IS_USER_DECLINE_SAVE_COOKIE = 'user_decline_save_cookie';

    /**
     * @var string
     */
    protected $_configSectionId = self::SECTION_ID;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Config\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $pageRepositoryInterface;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var Cookie
     */
    protected $cookie;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $currentStore;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Config\Model\Config              $config
     * @param \Magento\Cms\Api\PageRepositoryInterface  $pageRepositoryInterface
     * @param FilterProvider $filterProvider
     * @param Cookie $cookie
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Config\Model\Config $config,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        FilterProvider $filterProvider,
        Cookie $cookie,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($objectManager, $context);
        $this->resourceConnection       = $resourceConnection;
        $this->urlBuilder               = $context->getUrlBuilder();
        $this->pageRepositoryInterface  = $pageRepositoryInterface;
        $this->filterProvider           = $filterProvider;
        $this->cookie                   = $cookie;
        $this->currentStore             = $storeManager->getStore();
        $this->config                   = $config;
    }

    /**
     * Is module enabled
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    /**
     * Disable extension
     * @throws \Exception
     */
    public function disableExtension()
    {
        $resource = $this->resourceConnection;
        $connection = $resource->getConnection('core_write');
        $connection->delete(
            $resource->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $this->_configSectionId  . '/general/enabled')
            ]
        );
        $this->config->setDataByPath($this->_configSectionId  . '/general/enabled', 0);
        $this->config->save();
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isAccountExportEnabled($store = null)
    {
        return $this->moduleEnabled($store);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isAccountDeletionEnabled($store = null)
    {
        return $this->moduleEnabled($store);
    }

    /**
     * @param int|string $store
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPrivacyPolicyPageUrl($store = null)
    {
        $pageUrl = '';

        if ($pageId = $this->getConfig($this->_configSectionId . '/dashboard/privacy_policy_page', $store)) {
            $page = $this->pageRepositoryInterface->getById($pageId);

            if ($page) {
                $pageUrl = $this->urlBuilder->getUrl($page->getIdentifier());
            }
        }

        return $pageUrl;
    }

    /**
     * @param int|string $store
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCookiePolicyPageUrl($store = null)
    {
        $pageUrl = '';

        if ($pageId = $this->getConfig($this->_configSectionId . '/dashboard/cookie_policy_page', $store)) {
            $page = $this->pageRepositoryInterface->getById($pageId);

            if ($page) {
                $pageUrl = $this->urlBuilder->getUrl($page->getIdentifier());
            }
        }

        return $pageUrl;
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getProtectionOfficerEmail($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/dashboard/protection_officer_email', $store);
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function isGtmEnabled($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/gtm/enabled', $store);
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getGtmContainerId($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/gtm/container_id', $store);
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getEmailSenderName($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/sender_name', $store);
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getEmailSenderEmail($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/sender_email', $store);
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getEmailDownloadConfirmationTemplate($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/download_confirmation_template', $store);
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getEmailRemovalRequestTemplate($store = null)
    {
        return $this->getConfig($this->_configSectionId . '/email/removal_request_template', $store);
    }

    /**
     * @return string
     */
    public function getConfigSectionId()
    {
        return $this->_configSectionId;
    }

    /**
     * Get account delete deletion .
     *
     * @return int
     */
    public function getDeletionTime()
    {
        return 24 * 60 * 60;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getAnonymizationKey($store = null)
    {
        $key = trim($this->getConfig($this->_configSectionId . '/removal_settings/anonymization_key', $store));

        if (! $key) {
            $key = 'xxxx';
        }

        return $key;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCookieNoticeText($store = null)
    {
        $text = $this->getConfig($this->_configSectionId . '/cookie_consent/notice_text', $store);
        $html = $this->filterProvider->getPageFilter()->filter($text);
        return $html;
    }

    /**
     * @deprecated since 1.2.0 - use getAcceptCookieButtonLabel() instead
     * @param null $store
     * @return string
     */
    public function getCookieButtonLabel($store = null)
    {
        return (string)$this->getConfig($this->_configSectionId . '/cookie_consent/button_label', $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getAcceptCookieButtonLabel($store = null)
    {
        return (string)$this->getConfig($this->_configSectionId . '/cookie_consent/button_label', $store);
    }

    /**
     * @param null $store
     * @return string
     */
    public function getDeclineCookieButtonLabel($store = null)
    {
        return (string)$this->getConfig($this->_configSectionId . '/cookie_consent/decline_button_label', $store);
    }

    /**
     * Check if cookie restriction mode is enabled for this store
     *
     * @return bool
     */
    public function isCookieRestrictionModeEnabled()
    {
        return method_exists($this->cookie, 'isCookieRestrictionModeEnabled')
            ? (bool)$this->cookie->isCookieRestrictionModeEnabled()
            : (bool)$this->getConfig(Cookie::XML_PATH_COOKIE_RESTRICTION, $this->currentStore);
    }
}
