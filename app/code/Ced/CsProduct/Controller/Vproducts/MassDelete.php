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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProduct\Controller\Vproducts;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

class MassDelete extends \Ced\CsProduct\Controller\Vproducts
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Registry $registry
     */
public function __construct(
       \Magento\Backend\App\Action\Context $context,
        Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
         UrlFactory $urlFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->registry = $registry;
       
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
    	$vendorId = $this->_getSession()->getVendorId();
    	if(!$vendorId) {
            return $this->_redirect('csproduct/*/index', ['store' => $this->getRequest()->getParam('store')]);
        }
        $entity_ids = explode(',', $this->getRequest()->getParam('product_id'));


        $this->registry->register('isSecureArea', true);
        $redirectBack = false;
        
        if (!is_array($entity_ids) || empty($entity_ids)) {
        	$this->messageManager->addError(__('Please select Products(s).'));
        } else {
        	$productDeleted = 0;
            try {
        	   foreach ($entity_ids as $entityId) {
        	   	    $product_id = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                                    ->load($entityId)->getProductId();
        	   		$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id);        	  
        	   		if ($product && $product->getId()) {
        		  		$product->delete();
        		  	    $productDeleted++;
        	  		}
        		}
    		}
            catch (\Exception $e) {
            	$redirectBack = true;
            }
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $productDeleted)
        );
       return $this->_redirect('csproduct/*/index', ['store' => $this->getRequest()->getParam('store')]); 
    }
}
