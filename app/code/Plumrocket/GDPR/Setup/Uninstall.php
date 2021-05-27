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

namespace Plumrocket\GDPR\Setup;

use Plumrocket\GDPR\Model\ResourceModel\Revision as RevisionResource;
use Plumrocket\GDPR\Model\ResourceModel\Revision\History as RevisionHistoryResource;

/** Uninstall Plumrocket_GDPR */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    /**
     * Config section id
     *
     * @var string
     */
    protected $_configSectionId = 'prgdpr';

    /**
     * Patches to files
     *
     * @var Array
     */
    protected $_pathes = ['/app/code/Plumrocket/GDPR'];

    /**
     * Tables
     *
     * @var Array
     */
    protected $_tables = [
        'plumrocket_gdpr_removal_requests',
        'plumrocket_gdpr_export_log',
        'plumrocket_gdpr_consents_log',
        RevisionResource::MAIN_TABLE_NAME,
        RevisionHistoryResource::MAIN_TABLE_NAME,
    ];
}
