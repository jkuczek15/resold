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
namespace Ced\CsProduct\Ui\DataProvider\Product\Grouped;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class GroupedProductDataProvider extends ProductDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;
    
    protected $_vproduct;

    /**
     * GroupedProductDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param \Ced\CsMarketplace\Model\Vproducts $vproduct
     * @param ConfigInterface $config
     * @param StoreRepositoryInterface $storeRepository
     * @param array $meta
     * @param array $data
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
    	\Ced\CsMarketplace\Model\Vproducts $vproduct,
        ConfigInterface $config,
        StoreRepositoryInterface $storeRepository,
        array $meta = [],
        array $data = [],
        array $addFieldStrategies = [],
        array $addFilterStrategies = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->request = $request;
        $this->storeRepository = $storeRepository;
        $this->config = $config;
        $this->_vproduct = $vproduct;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
    	$vproducts = $this->_vproduct->getVendorProductIds();
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->addAttributeToFilter(
                'type_id',
                $this->config->getComposableTypes()
            );
            if ($storeId = $this->request->getParam('current_store_id')) {
                /** @var StoreInterface $store */
                $store = $this->storeRepository->getById($storeId);
                $this->getCollection()->setStore($store);
            }
            $this->getCollection()->addFieldToFilter('entity_id',array('in'=>$vproducts))->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }
}
