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

interface NotificationManagementInterface
{
	/**
	 * Register a device to receive push notifications
	 * 
	 * @return mixed[]
	 */
	public function registerDevice();

	/**
	 * Send a notification message to a device
	 *
	 * @return mixed[]
	 */
	public function sendNotificationMessage();
}
