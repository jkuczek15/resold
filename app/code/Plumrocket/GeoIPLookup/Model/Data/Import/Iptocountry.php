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

namespace Plumrocket\GeoIPLookup\Model\Data\Import;

use \Plumrocket\GeoIPLookup\Helper\Config;
use \Plumrocket\GeoIPLookup\Model\ResourceModel\IpToCountry as IpToCountryResource;

class Iptocountry extends \Plumrocket\GeoIPLookup\Model\Data\Import\AbstractModel
{
    /**
     * @var string
     */
    public $dataName = 'Iptocountry';

    /**
     * @var string
     */
    public $dataLabel = 'IpToCountry GeoIP Database';

    /**
     * @var string
     */
    public $pathSourceFile = Config::IPTOCOUNTRY_SOURCE_FILE;

    /**
     * @var string
     */
    public $fileName = 'IpToCountry.csv';

    /**
     * @var array
     */
    public $dataMapping = [
        'ip_from' => 0,
        'ip_to' => 1,
        'country_iso_code2' => 4,
        'country_iso_code3' => 5,
        'country_name' => 6
    ];

    /**
     * @var string
     */
    public $tableName = IpToCountryResource::MAIN_TABLE_NAME;

}