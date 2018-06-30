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
use Magento\Framework\Controller\ResultFactory;

/**
 * Backend reload of product create/edit form
 */
class Reload extends \Ced\CsProduct\Controller\Vproducts
{
	
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
			Session $customerSession,
			\Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter $stockFilter,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			UrlFactory $urlFactory,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
			\Magento\Framework\Module\Manager $moduleManager
	) {
		$this->stockFilter = $stockFilter;
		parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
		$this->productBuilder = $productBuilder;
		
	}
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            return $this->resultFactory->create(ResultFactory::TYPE_FORWARD)->forward('noroute');
        }

        $product = $this->productBuilder->build($this->getRequest());

        
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultLayout->getLayout()->getUpdate()->addHandle(['csproduct_vproducts_' . $product->getTypeId()]);
        //$resultLayout->getLayout()->getUpdate()->removeHandle('default');
       // $resultLayout->setHeader('Content-Type', 'application/json', true);
        return $resultLayout;
    }
}
