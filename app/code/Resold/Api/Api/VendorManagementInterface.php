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

interface VendorManagementInterface
{
	/**
	 * Create a vendor
	 *
	 * @return mixed[]
	 */
	public function createVendor();

	/**
	 * Get vendor orders
	 * @return mixed[]
	 */
	public function getVendorOrders();

	/**
	 * Get Stripe Dashboard URL
	 * @return string
	 */
	public function getStripeUrl();
}
