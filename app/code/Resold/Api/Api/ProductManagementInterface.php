<?php
/**
 * Resold
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category    Resold
 * @package     Resold
 * @author      Resold Core Team <dev@resold.us>
 * @copyright   Copyright Resold (https://resold.us/)
 * @license     https://resold.us/license-agreement
 */
namespace Resold\Api\Api;

interface ProductManagementInterface
{
	/**
	 * Create a product
	 *
	 * @param string $name
 	 * @param double $price
 	 * @param int $topCategory
 	 * @param int $condition
	 * @param string $details
	 * @param string $localGlobal
	 * @param string[] $imagePaths
	 * @param double $latitude
 	 * @param double $longitude
 	 * @param string $itemSize
	 * @return mixed[]
	 */
	public function createProduct($name, $price, $topCategory, $condition, $details, $localGlobal, $imagePaths, $latitude, $longitude, $itemSize);

	/**
	 * Get a product
	 *
	 * @param int $productId
	 * @return mixed[]
	 */
	public function getProduct($productId);

	/**
	 * Set delivery ID
	 *
	 * @param int $productId
 	 * @param string $deliveryId
	 * @return int
	 */
	public function setDelivery($productId, $deliveryId);

	/**
	 * Set price
	 *
	 * @param int $productId
 	 * @param string $newPrice
	 * @return int
	 */
	public function setPrice($productId, $newPrice);

	/**
	 * Check if a product is mine
	 *
	 * @param int $productId
	 * @return mixed[]
	 */
	public function isProductMine($productId);
}
