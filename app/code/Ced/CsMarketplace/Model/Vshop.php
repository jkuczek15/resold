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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model;

use Magento\Framework\Api\AttributeValueFactory;

class Vshop extends FlatAbstractModel
{

    const ENABLED = 1;
    const DISABLED = 2;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_objectManager = $objectInterface;
        $this->_registry = $this->_objectManager->get('Magento\Framework\Registry');
        $this->_customerSession = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getCustomerSession();

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }


    /**
     * Initialize vproducts model
     */
    protected function _construct()
    {
        $this->_init('Ced\CsMarketplace\Model\ResourceModel\Vshop');
    }

    public function saveShopStatus(array $vendorIds, $shop_disable)
    {
        $vendors = [];
        if (count($vendorIds) > 0) {
            foreach ($vendorIds as $vendorId) {
                $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vshop')
                        ->loadByField(['vendor_id'], [$vendorId]);
                if ($model && $model->getId()) {
                    if ($model->getShopDisable() != $shop_disable) {
                        $model->setShopDisable($shop_disable)->save();
                        $vendors[] = $model->getVendorId();
                        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($model->getVendorId());
                        if ($vendor && $vendor->getId()) {
                            $this->_objectManager->create('Ced\CsMarketplace\Helper\Mail')->sendShopEmail($model->getShopDisable(), '', $vendor);
                        }
                    }
                } else {
                    $vshop = $this->_objectManager->get('Ced\CsMarketplace\Model\Vshop');
                    $vshop->setVendorId($vendorId)->setShopDisable($shop_disable)->save();
                    $vendors[] = $vendorId;
                    $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($model->getVendorId());
                    if ($vendor && $vendor->getId()) {
                        $this->_objectManager->create('Ced\CsMarketplace\Helper\Mail')->sendShopEmail($model->getShopDisable(), '', $vendor);
                    }
                }
            }
        }
        if (count($vendors) > 0) {
            $this->changeProductsStatus($vendorIds, $shop_disable);
        }
        return count($vendors);
    }

    /**
     *Change Products Status (Hide/show products from frontend on vendor approve/disapprove)
     *
     * @params array $vendorIds,int $status
     * @return boolean
     */
    public function changeProductsStatus($vendorIds, $status)
    {
        if (is_array($vendorIds)) {
            foreach ($vendorIds as $vendorId) {
                $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('', $vendorId);
                foreach ($collection as $row) {
                    $productId = $row->getProductId();
                    if ($status == self::DISABLED) {
                        $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                            ->getCollection()->addFieldtoFilter('product_id', $productId);
                        foreach ($statusCollection as $statusrow) {
                            $this->_objectManager->get('Magento\Catalog\Model\Product\Action')->updateAttributes([$productId], ['status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED], $statusrow->getStoreId());
                        }
                    } elseif ($status == self::ENABLED) {
                        $statusCollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts\Status')
                                            ->getCollection()->addFieldtoFilter('product_id', $productId);
                        foreach ($statusCollection as $statusrow) {
                            $this->_objectManager->get('Magento\Catalog\Model\Product\Action')->updateAttributes([$productId], ['status' => $statusrow->getStatus()], $statusrow->getStoreId());
                        }
                    }
                    
                }
            }
        }
        return $this;
    }

}


