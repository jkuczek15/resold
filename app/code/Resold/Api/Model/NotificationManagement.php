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

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Kreait\Firebase\Factory $factory
   )
  {
    $this->factory = $factory->withServiceAccount('/var/www/html/firebase-adminsdk-key.json');
  }

	/**
	 * {@inheritdoc}
	 */
  public function sendNotificationMessage($deviceToken, $title, $body, $imageUrl)
	{
    $messaging = $this->factory->createMessaging();

    // setup notification
    $message = CloudMessage::withTarget('token', $deviceToken)->withNotification([
      'title' => $title,
      'body' => $body,
      'image' => $imageUrl
    ])->withData([
      'image' => $imageUrl
    ]);

    // send notification
    $messaging->send($message);
  }// end function sendNotificationMessage
}
