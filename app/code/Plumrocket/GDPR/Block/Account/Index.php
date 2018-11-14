<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block\Account;

/**
 * Customer gdpr account block.
 */
class Index extends \Plumrocket\GDPR\Block\GDPR
{
    /**
     * Get export page url.
     *
     * @return string
     */
    public function getExportPageUrl()
    {
        return $this->getUrl('prgdpr/account/export');
    }

    /**
     * Get delete page url.
     *
     * @return string
     */
    public function getDeletingPageUrl()
    {
        return $this->getUrl('prgdpr/account/delete');
    }

    /**
     * Get undodelete controller.
     *
     * @return string
     */
    public function getUndoDeletePageURL()
    {
        return $this->getUrl('prgdpr/delete/undodelete');
    }

    /**
     * Get Unsubscribe page url.
     *
     * @return string
     */
    public function getUnsubscribePageURL()
    {
        return $this->getUrl('newsletter/manage');
    }
}
