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
   )
  {
  }

	/**
	 * {@inheritdoc}
	 */
  public function processPostmatesEvent($kind, $id, $delivery_id, $status, $data, $created, $live_mode)
  {
    var_dump($data);
    exit;
  }// end function processPostmatesEvent
}
