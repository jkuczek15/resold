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

interface WebhookManagementInterface
{
	/**
	 * Process a Postmates delivery event
	 * 
	 * @param string $kind
	 * @param string $id
	 * @param string $delivery_id
	 * @param string $status
	 * @param mixed[] $data
	 * @param string $created
	 * @param bool $live_mode
	 * @return mixed[]
	 */
	public function processPostmatesEvent($kind, $id, $delivery_id, $status, $data, $created, $live_mode);
}
