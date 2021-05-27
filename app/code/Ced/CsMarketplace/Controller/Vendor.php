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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Vendor extends \Magento\Framework\App\Action\Action
{
    public static $openActions = array(
            'create',
            'login',
            'logoutsuccess',
            'forgotpassword',
            'forgotpasswordpost',
            'confirm',
            'confirmation',
            'approval',
            'approvalPost',
            'checkAvailability',
            'denied',
            'noRoute'
        );
        
    public $_allowedResource = true;
    
    /**
    * 
    * @var Session 
    */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /** 
     * @var \Magento\Framework\UrlInterface 
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
        
     /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param Context     $context
     * @param Session     $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->urlModel = $urlFactory;
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_moduleManager = $moduleManager;
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Registry');
        if(!$registry->registry('vendorPanel'))
            $registry->register('vendorPanel', 1);

        if(!$registry->registry('vendor'))
            $registry->register('vendor',$this->session->getVendor());
        parent::__construct($context);
        $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }
    
    /**
* 
 * Retrieve customer session model object
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }
    
    /**
     * Get list of actions that are allowed for not authorized users
     *
     * @return string[]
     */
    protected function getAllowedActions()
    {
        return self::$openActions;
    }

    /**
     * Dispatch request
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {     
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$this->getRequest()->isDispatched()) {
            parent::dispatch($request);
        }        
        $action = strtolower($this->getRequest()->getActionName());
        $pattern = '/^(' . implode('|', $this->getAllowedActions()) . ')$/i';
        
        if (!preg_match($pattern, $action)) {
            if (!$this->authenticate($this)) {
                if($this->getRequest()->isAjax()) {
                    $loginUrl = $this->urlModel->create()->getUrl('csmarketplace/account/login', ['_secure' => $this->getRequest()->isSecure()]);

                    $ajaxResponse = [];
                    $ajaxResponse['ajaxExpired'] = true;
                    $ajaxResponse['ajaxRedirect'] = $loginUrl;
                    $this->getResponse()->setBody(json_encode($ajaxResponse));
                    $resultJson = $this->resultJsonFactory->create();
                    return $resultJson->setData($ajaxResponse);
                }

                $this->_actionFlag->set('', 'no-dispatch', true);
            }  elseif(!$this->_objectManager->get('Ced\CsMarketplace\Helper\Acl')->isEnabled()) {
                $resultRedirect->setPath('customer/account/');
                return $resultRedirect;
            } elseif (!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->authenticate($this->session->getCustomerId())) {
                $this->session->unsVendorId();
                $this->session->unsVendor();
                $resultRedirect->setPath('*/account/approval');
                return $resultRedirect;
            }
        } else {
            $this->session->setNoReferer(true);
        }
        $result = parent::dispatch($request);
        $this->_eventManager->dispatch(
            'ced_csmarketplace_predispatch_action', [
            'session' => $this->session,
            ]
        );
        $this->session->unsNoReferer(false);
        return $result;
    }
    
    /**
     * Authenticate controller action by login customer
     *
     * @param  Mage_Core_Controller_Varien_Action $action
     * @param  bool                               $loginUrl
     * @return bool
     */
    public function authenticate(Vendor $action, $loginUrl = null)
    {            
        if (!$this->session->isLoggedIn()) {
            if($action->getRequest()->isAjax()) {
                $this->session->setBeforeVendorAuthUrl($this->urlModel->create()->getUrl('*/vendor/', ['_secure' => true,'_current' => true]));
            } else {
                $oAuthUrl = $this->urlModel->create()->getUrl('*/*/*', ['_current'=>true]);
                if(!preg_match('/'.preg_quote('csmarketplace').'/i', $oAuthUrl)) {
                    $oAuthUrl = $this->urlModel->create()->getUrl('seller/sell/form', ['_current'=>true]); 
                }
                $this->session->setBeforeVendorAuthUrl($oAuthUrl);
            }
            if (is_null($loginUrl)) {
                $url='csmarketplace/account/login';
                $loginUrl = $this->urlModel->create()->getUrl($url, ['_secure' => $action->getRequest()->isSecure()]);
            }
            if ($action->getRequest()->isAjax()) {
                $ajaxResponse = [];
                $ajaxResponse['ajaxExpired'] = true;
                $ajaxResponse['ajaxRedirect'] = $loginUrl;
                $action->getResponse()->setBody(json_encode($ajaxResponse));
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($response);
            }
            $action->getResponse()->setRedirect($loginUrl);
            return false;
        }
        if($this->session->isLoggedIn() && $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->authenticate($this->session->getCustomerId())) {    
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($this->session->getCustomerId());
            if($vendor && $vendor->getId()) {
                $this->session->setVendorId($vendor->getId());
                $this->session->setVendor($vendor->getData());
                $this->_eventManager->dispatch(
                    'ced_csmarketplace_vendor_authenticate_after', [
                    'session' => $this->session
                    ]
                );
            }
        }
        $this->_eventManager->dispatch(
            'ced_csmarketplace_vendor_acl_check', [
            'current' => $this,
            'action'  => $action,
            ]
        );
        return $this->_allowedResource;
    }
    
    public function execute()
    {    
    }
}
