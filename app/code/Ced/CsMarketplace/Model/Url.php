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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
//use Magento\Customer\Model\Session;

/**
 * Customer url model
 */
class Url
{
    /**
     * Route for customer account login page
     */
    const ROUTE_ACCOUNT_LOGIN = 'market/account/login';

    /**
     * Config name for Redirect Customer to Account Dashboard after Logging in setting
     */
    const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'customer/startup/redirect_dashboard';

    /**
     * Query param name for last url visited
     */
    const REFERER_QUERY_PARAM_NAME = 'referer';

    /**
     * @var UrlInterface
     */
    protected $cedUrlBuilder;

    /**
     * @var RequestInterface
     */
    protected $cedRequest;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    //  protected $customerSession;

    /**
     * @var EncoderInterface
     */
    protected $cedUrlEncoder;

    /**
     * @param Session              $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface     $cedRequest
     * @param UrlInterface         $cedUrlBuilder
     * @param EncoderInterface     $cedUrlEncoder
     */
    public function __construct(
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $cedRequest,
        UrlInterface $cedUrlBuilder,
        EncoderInterface $cedUrlEncoder
    ) {
        $this->cedRequest = $cedRequest;
        $this->cedUrlBuilder = $cedUrlBuilder;
        $this->scopeConfig = $scopeConfig;
      //   $this->customerSession = $customerSession;
        $this->cedUrlEncoder = $cedUrlEncoder;
    }

    /**
     * Retrieve base url
     *
     * @return string
     */
    public function getBaseUrl() 
    {
        return $this->cedUrlBuilder->getUrl();
    }
    
    /**
     * Retrieve base url
     *
     * @return string
     */
    public function getMediaUrl() 
    {
        return $this->cedUrlBuilder->getUrl('pub').'media';
    }
    
    /**
     * Retrieve customer login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->cedUrlBuilder->getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }

    /**
     * Retrieve customer logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/logout');
    }

    /**
     * Retrieve customer login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl()
    {
        $params = [];
        if ($this->cedRequest->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = [
                self::REFERER_QUERY_PARAM_NAME => $this->cedRequest->getParam(self::REFERER_QUERY_PARAM_NAME),
            ];
        }
        return $this->cedUrlBuilder->getUrl('market/account/loginPost', $params);
    }

    /**
     * Retrieve customer dashboard url
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/vendor/index');
    }

    /**
     * Retrieve customer account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/vendor');
    }

    /**
     * Retrieve customer register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/create');
    }

    /**
     * Retrieve customer register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/createpost');
    }
    
    /**
     * Retrieve vendor approval form url
     *
     * @return string
     */
    public function getApprovalUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/approval');
    }

    /**
     * Retrieve vendor approval form post url
     *
     * @return string
     */
    public function getApprovalPostUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/approvalpost');
    }

    /**
     * Retrieve customer account edit form url
     *
     * @return string
     */
    public function getProfileEditUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/vendor/profile');
    }

    /**
     * Retrieve customer edit POST URL
     *
     * @return string
     */
    public function getProfileEditPostUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/vendor/save');
    }

    /**
     * Retrieve url of forgot password page
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/forgotpassword');
    }
    
    /**
     * Retrieve url of forgot password post page
     *
     * @return string
     */
    public function getForgotPasswordPostUrl()
    {
        return $this->cedUrlBuilder->getUrl('csmarketplace/account/forgotpasswordpost');
    }

    /**
     * Retrieve confirmation URL for Email
     *
     * @param  string $email
     * @return string
     */
    public function getEmailConfirmationUrl($email = null)
    {
        return $this->cedUrlBuilder->getUrl('customer/account/confirmation', ['email' => $email]);
    }
    
    public function getShopUrl($url)
    {
        return $this->cedUrlBuilder->getUrl($url, ['_secure'=>true]);
    }

    /**
     * Retrieve parameters of customer login url
     *
     * @return array
     */
    public function getLoginUrlParams()
    {
        $params = [];
        $referer = $this->cedRequest->getParam(self::REFERER_QUERY_PARAM_NAME);
        $this->_objectManager=\Magento\Framework\App\ObjectManager::getInstance();
        $this->customerSession = $this->_objectManager->create('\Magento\Customer\Model\Session');
        if (!$referer
            && !$this->scopeConfig->isSetFlag(
                self::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD,
                ScopeInterface::SCOPE_STORE
            )
            && !$this->customerSession->getNoReferer()
        ) {
            $referer = $this->cedUrlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            $referer = $this->cedUrlEncoder->encode($referer);
        }

        if ($referer) {
            $params = [self::REFERER_QUERY_PARAM_NAME => $referer];
        }

        return $params;
    }
}
