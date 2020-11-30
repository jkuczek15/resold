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
namespace Resold\Api\Model;

class WebhookManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Resold\Api\Logger\Logger $logger
   )
  {
    $this->logger = $logger;
  }

	/**
	 * {@inheritdoc}
	 */
  public function processPostmatesEvent($kind, $id, $delivery_id, $status, $data, $created, $live_mode)
  {
    $this->logger->info(json_encode([
      'type' => 'PostmatesEvent',
      'kind' => $kind,
      'id' => $id,
      'delivery_id' => $delivery_id,
      'status' => $status,
      'data' => $data,
      'created' => $created,
      'live_mode' => $live_mode
    ]));
  }// end function processPostmatesEvent
}
