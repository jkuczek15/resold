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
 * @package     Ced_Fyndiq
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\FbNative\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_productPriceIndexerProcessor;

    /**
     * MassActions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        Filter $filter,
        CollectionFactory $collectionFactory
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        parent::__construct($context, $productBuilder);
    }

    public function execute()
    {
        $attribute = $this->getRequest()->getParam('is_facebook');
        $collection = $this->_objectManager->create('Magento\Catalog\Model\Product')
            ->getCollection();
        $productIds = $this->filter->getCollection($collection)->getAllIds();

        if (!is_array($productIds) && !$attribute) {
            $this->messageManager->addError(__('Please select Product(s).'));
        } elseif ($attribute == "false") {
            $productIds = $this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection()->getAllIds();
        }

        if (!empty($productIds)) {
            try {
                foreach ($productIds as $productId) {
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                    if($product->getTypeId()=="configurable") {
                        $productType = $product->getTypeInstance();
                        $products = $productType->getUsedProducts($product);
                        foreach ($products as $productData) {
                            $productData->setIsFacebook(1);
                            $productData->getResource()->saveAttribute($productData,'is_facebook');
                        }
                    }
                    $product->setIsFacebook(1);
                    $product->getResource()->saveAttribute($product,'is_facebook');
                }
                $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been added to the store.', count($productIds)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}
