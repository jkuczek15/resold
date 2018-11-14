<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Model\Account\Processors;

use Plumrocket\GDPR\Api\DataProcessorInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Model\ProductFactory;

/**
 * Processor customer wishlist.
 */
class CustomerWishlist implements DataProcessorInterface
{
    /**
     * @var WishlistProviderInterface
     */
    private $wishlistProvider;

    /**
     * @var WishlistFactory
     */
    private $wishlistFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var array
     */
    private $dataExport;

    /**
     * CustomerWishlist constructor.
     *
     * @param WishlistProviderInterface $wishlistProvider
     * @param WishlistFactory $wishlistFactory
     * @param ProductFactory $productFactory
     * @param array $dataExport
     */
    public function __construct(
        WishlistProviderInterface $wishlistProvider,
        WishlistFactory $wishlistFactory,
        ProductFactory $productFactory,
        array $dataExport = []
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->wishlistFactory = $wishlistFactory;
        $this->productFactory = $productFactory;
        $this->dataExport = $dataExport;
    }

    /**
     * Executed upon exporting customer data.
     *
     * Expected return structure:
     *      array(
     *          array('HEADER1', 'HEADER2', 'HEADER3', ...),
     *          array('VALUE1', 'VALUE2', 'VALUE3', ...),
     *          ...
     *      )
     *
     * @param CustomerInterface $customer
     * @return array
     */
    public function export(CustomerInterface $customer)
    {
        $wishlistData = $this->wishlistProvider->getWishlist()->getItemCollection()->getItems();
        $returnData = [];
        $i=0;

        if (!$wishlistData) {
            return null;
        }

        foreach ($this->dataExport as $key => $title) {
            $returnData[$i][] = $title;
        }

        $i++;

        $productLoader = $this->productFactory->create();

        foreach ($wishlistData as $item) {
            $itemData = $item->getData();

            foreach ($this->dataExport as $key => $title) {
                if ($key == 'product') {
                    $product = isset($itemData['product'])
                        ? $itemData['product']
                        : $productLoader->load($itemData['product_id']);
                    $itemData['product'] = $product->getName() . ' (' . $product->getSku() . ')';
                }

                $returnData[$i][] = (isset($itemData[$key]) ? $itemData[$key] : '');
            }

            $i++;
        }

        return $returnData;
    }

    /**
     * Executed upon customer data deletion.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function delete(CustomerInterface $customer)
    {
        $this->processWishlist($customer->getId());
    }

    /**
     * Executed upon customer data anonymization.
     *
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function anonymize(CustomerInterface $customer)
    {
        $this->processWishlist($customer->getId());
    }

    /**
     * Clear customer wishlist.
     *
     * @param int $customerId
     *
     * @return void
     * @throws \Exception
     */
    private function processWishlist(int $customerId)
    {
        $wishlist = $this->wishlistFactory->create()->loadByCustomerId($customerId);
        $wishlist->getResource()->delete($wishlist);
    }
}
