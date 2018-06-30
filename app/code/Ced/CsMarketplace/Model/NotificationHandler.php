<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\CsMarketplace\Model;

/**
 * Class NotificationHandler used for getting notifications from all notification classes
 */
class NotificationHandler 
{
    /**
     * @var array $params
     */
    private $notificationList;

    /**
     * @param array $notificationList
     */
    public function __construct(
        array $notificationList = []
    ) {
        
        $this->notificationList = $notificationList;
    }

    public function getNotifications(){
        $notifications = [];
      
        foreach($this->notificationList as $notification){
            $notifications = array_merge($notification->getNotifications(),$notifications);
        }
        return $notifications;
    }
}
