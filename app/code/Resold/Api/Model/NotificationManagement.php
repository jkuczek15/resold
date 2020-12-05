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

use \Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\ServiceAccount;

class NotificationManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Resold\Api\Logger\Logger $logger,
    \Kreait\Firebase\Factory $factory
   )
  {
    $this->logger = $logger;
    $this->factory = $factory->withServiceAccount(ServiceAccount::fromJsonFile('/var/www/html/firebase-adminsdk-key.json'));
  }

	/**
	 * {@inheritdoc}
	 */
  public function sendNotificationMessage($deviceToken, $title, $body, $imageUrl, $chatId = null)
	{
    $messaging = $this->factory->createMessaging();

    // setup notification
    $message = CloudMessage::withTarget('token', $deviceToken)->withNotification([
      'title' => $title,
      'body' => $body,
      'image' => $imageUrl,
      'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    ])->withData([
      'image' => $imageUrl,
      'chatId' => $chatId
    ]);

    // send notification
    $messaging->send($message);
  }// end function sendNotificationMessage
}
