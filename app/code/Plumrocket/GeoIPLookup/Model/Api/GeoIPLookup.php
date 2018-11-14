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

namespace Plumrocket\GeoIPLookup\Model\Api;

class GeoIPLookup implements \Plumrocket\GeoIPLookup\Api\GeoIPLookupInterface
{
    /**
     * @var \Plumrocket\GeoIPLookup\Model\GeoIPLookup
     */
    private $geoIPLookup;

    /**
     * GeoIPLookup constructor.
     *
     * @param \Plumrocket\GeoIPLookup\Model\GeoIPLookup $geoIPLookup
     */
    public function __construct(
        \Plumrocket\GeoIPLookup\Model\GeoIPLookup $geoIPLookup
    ) {
        $this->geoIPLookup = $geoIPLookup;
    }

    /**
     * Returns GeoIpData
     *
     * @param string $ip
     * @api
     * @return array
     */
    public function getGeoIpData($ip)
    {
        return [
            $this->geoIPLookup->getGeoLocation($ip)
        ];
    }
}