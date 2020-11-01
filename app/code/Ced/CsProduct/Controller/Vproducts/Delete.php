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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProduct\Controller\Vproducts;

use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

class Delete extends \Ced\CsProduct\Controller\Vproducts
{
    protected $messageManager;

    protected $registry;

    public function __construct(
       \Magento\Backend\App\Action\Context $context,
        Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
         UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->registry = $registry;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $vendorId = $this->_getSession()->getVendorId();
        if(!$vendorId) 
            return false;

        $id = $this->getRequest()->getParam('id');
        $vendorProduct = false;
        if($id && $vendorId){
            $vendorProduct = $this->_objectManager->get('\Ced\CsMarketplace\Model\Vproducts')->isAssociatedProduct($vendorId, $id);
        }
        $this->registry->register('isSecureArea', true);

        $redirectBack = false;
        if(!$vendorProduct)
            $redirectBack = true;

        elseif($id) {
            $product = $this->_objectManager->get('\Magento\Catalog\Model\Product')->load($id);
            try {
                if($product && $product->getId()) {
                    $product->delete();
                    $this->messageManager->addSuccess(__('Your Product Has Been Sucessfully Deleted'));
                }               
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if ($redirectBack) {
            $this->messageManager->addError(__('Unable to delete the product'));
        }

        return $this->_redirect('*/*/index',['store' => $this->getRequest()->getParam('store')]); 
    }


}
