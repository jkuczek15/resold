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
 
namespace Ced\CsProduct\Controller\VProducts;

class UpsellGrid extends \Ced\CsProduct\Controller\Vproducts
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    /*public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }*/

    /**
     * Get upsell products grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        //$this->resultPageFactory = $this->_objectManager->get('Magento\Framework\View\Result\PageFactory');
        $this->productBuilder = $this->_objectManager->get('Magento\Catalog\Controller\Adminhtml\Product\Builder');
        //$this->urlBuilder = $this->_objectManager->get('Magento\Framework\UrlInterface');
        $this->resultLayoutFactory = $this->_objectManager->get('Magento\Framework\View\Result\LayoutFactory');

        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('catalog.product.edit.tab.upsell')
            ->setProductsRelated($this->getRequest()->getPost('products_upsell', null));
        return $resultLayout;
    }
}
