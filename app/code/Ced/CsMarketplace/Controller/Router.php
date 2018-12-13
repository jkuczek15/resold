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

namespace Ced\CsMarketplace\Controller;


class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\ActionFactory     $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
    }

    /**
     * Validate and Match
     *
     * @param  \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        // $custom_suffix = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')
        //                       ->getStoreConfig('ced_vseo/general/marketplace_url_suffix');
        // $suffix = $custom_suffix ? $custom_suffix : \Ced\CsMarketplace\Model\Vendor::VENDOR_SHOP_URL_SUFFIX;
        $suffix = 'vendor_shop';
        $url_path = 'vendor_shop/';
        if($this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Ced_CsSeoSuite') && $this->_objectManager->get('Ced\CsSeoSuite\Helper\Data')->isEnabled()){
            $url_path = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vseo/general/marketplace_url_key').'/';
        }
        if(strpos($identifier, $url_path) !== false && strpos($identifier, $suffix) !== false) {
            $urls = explode('/', $identifier);
            $url = explode($suffix, end($urls));
            $request->setModuleName('market')->setControllerName('vshops')->setActionName('view')->setParam('shop_url', $url[0]);
            //$request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return ;
        }

    }

}
