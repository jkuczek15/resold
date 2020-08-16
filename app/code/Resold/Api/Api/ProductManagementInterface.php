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
	 * @param int[] $localGlobal
	 * @param string[] $imagePaths
	 * @param double $latitude
 	 * @param double $longitude
	 * @return mixed
	 */
	public function createProduct($name, $price, $topCategory, $condition, $details, $localGlobal, $imagePaths, $latitude, $longitude);
}
