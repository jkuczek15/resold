<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model\Account;

use Magento\Customer\Model\Session;
use Ced\CsMarketplace\Model\Url as CustomerUrl;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Url\DecoderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Redirect extends \Magento\Customer\Model\Account\Redirect
{
    /**
     * @var RequestInterface
     */
    protected $csRequest;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $vendorStoreManager;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var VendorUrl
     */
    protected $vendorUrl;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var CsSession
     */
    protected $CsSession;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @param RequestInterface      $csRequest
     * @param Session               $customerSession
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $vendorStoreManager
     * @param UrlInterface          $url
     * @param DecoderInterface      $urlDecoder
     * @param CustomerUrl           $vendorUrl
     * @param RedirectFactory       $resultRedirectFactory
     */
    public function __construct(
        RequestInterface $csRequest,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $vendorStoreManager,
        UrlInterface $url,
        DecoderInterface $urlDecoder,
        CustomerUrl $vendorUrl,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->csRequest = $csRequest;
        $this->csSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
        $this->vendorStoreManager = $vendorStoreManager;
        $this->url = $url;
        $this->urlDecoder = $urlDecoder;
        $this->vendorUrl = $vendorUrl;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    /**
     * Retrieve redirect
     *
     * @return ResultRedirect
     */
    public function getRedirect()
    {
        $this->updateLastCustomerId();
        $this->prepareRedirectUrl();

        /**
         * @var ResultRedirect $resultRedirect
         */
        $csResultRedirect = $this->resultRedirectFactory->create();
        $csResultRedirect->setUrl($this->csSession->getBeforeVendorAuthUrl(true));
        return $csResultRedirect;
    }

    /**
     * Update last customer id, if required
     *
     * @return void
     */
    protected function updateLastCustomerId()
    {
        $lastCustomerId = $this->csSession->getLastCustomerId();
        if (isset($lastCustomerId)
            && $this->csSession->isLoggedIn()
            && $lastCustomerId != $this->csSession->getId()
        ) {
            $this->csSession->unsBeforeAuthUrl()
                ->setLastCustomerId($this->csSession->getId());
        }
    }

    /**
     * Prepare redirect URL
     *
     * @return void
     */
    protected function prepareRedirectUrl()
    {
        $baseUrl = $this->vendorStoreManager->getStore()->getBaseUrl();

        $url = $this->csSession->getBeforeVendorAuthUrl();
        if (!$url) {
            $url = $baseUrl;
        }

        switch ($url) {
        case $baseUrl:
            if ($this->csSession->isLoggedIn()) {
                $this->processLoggedCustomer();
            } else {
                $this->applyRedirect($this->vendorUrl->getLoginUrl());
            }
            break;

        case $this->vendorUrl->getLogoutUrl():
            $this->applyRedirect($this->vendorUrl->getDashboardUrl());
            break;

        default:
            if (!$this->csSession->getAfterVendorAuthUrl()) {
                $this->csSession->setAfterVendorAuthUrl($this->csSession->getBeforeVendorAuthUrl());
            }
            if ($this->csSession->isLoggedIn()) {
                $this->applyRedirect($this->csSession->getAfterVendorAuthUrl(true));
            }
            break;
        }
    }

    /**
     * Prepare redirect URL for logged in customer
     *
     * Redirect customer to the last page visited after logging in.
     *
     * @return void
     */
    protected function processLoggedCustomer()
    {
        // Set default redirect URL for logged in customer
        $this->applyRedirect($this->vendorUrl->getAccountUrl());

        if (!$this->scopeConfig->isSetFlag(
            CustomerUrl::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
            ScopeInterface::SCOPE_STORE
        )
        ) {
            $csReferer = $this->csRequest->getParam(CustomerUrl::REFERER_QUERY_PARAM_NAME);
            if ($csReferer) {
                $csReferer = $this->urlDecoder->decode($csReferer);
                if ($this->url->isOwnOriginUrl()) {
                    $this->applyRedirect($csReferer);
                }
            }
        } elseif ($this->csSession->getAfterVendorAuthUrl()) {
            $this->applyRedirect($this->csSession->getAfterVendorAuthUrl(true));
        }
    }

    /**
     * Prepare redirect URL
     *
     * @param  string $url
     * @return void
     */
    private function applyRedirect($url)
    {
        $this->csSession->setBeforeVendorAuthUrl($url);
    }
}
