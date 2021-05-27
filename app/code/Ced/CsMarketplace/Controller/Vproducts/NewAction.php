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

namespace Ced\CsMarketplace\Controller\Vproducts;

use \Magento\Catalog\Model\Product\Type;
use \Magento\Downloadable\Model\Product\Type as downloadableType;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class NewAction extends \Ced\CsMarketplace\Controller\Vproducts
{
    protected $_storeManager;
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
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $_storeManager
    ) {
        $this->_storeManager = $_storeManager;
    
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }
    
    public function execute()
    {
        $allowedType = $this->_objectManager->create('Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Type')->getAllowedType($this->_storeManager->getStore()->getId());
        $resultPage = $this->resultPageFactory->create();
        $secretkey = time();
        $type = $this->getRequest()->getParam('type', $secretkey);
        if ($type == $secretkey || (in_array($type, $allowedType))) {
            $update = $resultPage->getLayout()->getUpdate();
            $update->addHandle('default');
            //$this->_view->addActionLayoutHandles();
            $resultPage->initLayout();
            
            switch ($type){
            	
            case Type::TYPE_SIMPLE : $update->addHandle('csmarketplace_vproducts_simple');
                break;
            case Type::TYPE_VIRTUAL : $update->addHandle('csmarketplace_vproducts_virtual');
                break;
            case downloadableType::TYPE_DOWNLOADABLE : $update->addHandle('csmarketplace_vproducts_downloadable');
                break;
            default: $update->addHandle('csmarketplace_vproducts_type');
                break;                    
            }
            $resultPage->getConfig()->publicBuild();
            $resultPage->getConfig()->getTitle()->set(__('New')." ".__('Product'));
            return $resultPage;
            //$this->renderLayout ();
        } else {
        	$this->messageManager->addError('Please Select Product Type First To Create Product');
            return $this->_redirect('*/*/new');

        }
    }
}
