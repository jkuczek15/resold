<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
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

use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddClassToBody
 *
 * @package Plumrocket\GDPR\Observer
 */
class AddClassToBody implements ObserverInterface
{
    /** @var PageConfig */
    private $pageConfig;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ThemeProviderInterface */
    private $themeProvider;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * AddClassToBody constructor.
     *
     * @param PageConfig             $pageConfig
     * @param ScopeConfigInterface   $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        PageConfig $pageConfig,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->pageConfig = $pageConfig;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $themeId = $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        $themeData = $this->themeProvider->getThemeById($themeId)->getData();
        $bodyClassVandor = "prgdpr-" . mb_strstr(mb_strtolower($themeData['code']), '/', true);
        $bodyClassTheme = "prgdpr-" . str_replace("/", "-", mb_strtolower($themeData['code']));
        $this->pageConfig->addBodyClass($bodyClassVandor)->addBodyClass($bodyClassTheme);
    }
}
