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

namespace Ced\CsProduct\Controller\Grouped\Edit;

use Magento\Framework\Registry;
use Magento\Catalog\Model\ProductFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;

class Popup extends \Ced\CsProduct\Controller\Vproducts
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $factory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    protected $resultPageFactory;

    protected $_session;
    
    public function __construct(
    		\Magento\Backend\App\Action\Context $context,
    		Session $customerSession,
    		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
    		UrlFactory $urlFactory,
    		\Magento\Framework\Module\Manager $moduleManager,
    		Registry $registry,
    		ProductFactory $factory,
    		LoggerInterface $logger
    		//\Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
    		//\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
    ) {	
    	$this->registry = $registry;
    	$this->factory = $factory;
    	$this->logger = $logger;
    	parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
    	//$this->productBuilder = $productBuilder;
    	$this->resultPageFactory = $resultPageFactory;
    	//$this->resultForwardFactory = $resultForwardFactory;
    	$this->_session = $context->getSession();
    }
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $factory
     * @param \Psr\Log\LoggerInterface $logger
     */
    /* public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $factory,
        LoggerInterface $logger
    ) {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->logger = $logger;
        parent::__construct($context);
    } */

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_CsProducts::products');
    }

    /**
     * Get associated grouped products grid popup
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('id');

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->factory->create();
        $product->setStoreId($this->getRequest()->getParam('store', 0));

        $typeId = $this->getRequest()->getParam('type');
        if (!$productId && $typeId) {
            $product->setTypeId($typeId);
        }
        $product->setData('_edit_mode', true);

        if ($productId) {
            try {
                $product->load($productId);
            } catch (\Exception $e) {
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
                $this->logger->critical($e);
            }
        }

        $setId = (int)$this->getRequest()->getParam('set');
        if ($setId) {
            $product->setAttributeSetId($setId);
        }
        $this->_objectManager->get('\Magento\Framework\Registry')->register('current_product',$product);
        //$this->registry->register('current_product', $product);
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}
