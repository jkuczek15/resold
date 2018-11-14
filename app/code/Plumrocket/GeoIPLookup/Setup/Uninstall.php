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
 * @package     Plumrocket_GeoIPLookup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GeoIPLookup\Setup;

use Plumrocket\Base\Setup\AbstractUninstall;

class Uninstall extends AbstractUninstall
{
    protected $_configSectionId = 'prgeoiplookup';
    protected $_pathes = ['/app/code/Plumrocket/GeoIPLookup'];
    protected $_tables =
        [
            'prgeoiplookup_installed_versions',
            'prgeoiplookup_iptocountry',
            'prgeoiplookup_maxmindgeoip_city_blocks',
            'prgeoiplookup_maxmindgeoip_city_locations'
        ];
}
