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

namespace Plumrocket\GeoIPLookup\Helper;

use Plumrocket\GeoIPLookup\Helper\Data;

/**
 * Class Config use for retrieve module configuration
 */
class Config extends \Plumrocket\GeoIPLookup\Helper\Main
{
    /**
     * Name of Maxmind Group
     */
    const MAXMIND_GROUP = 'maxmindgeoip';

    /**
     * Name of IpToCountry Group
     */
    const IPTOCOUNTRY_GROUP = 'iptocountry';

    /**
     * Name of Nekudo Group
     */
    const NEKUDO_GROUP = 'nekudogeoip';

    /**
     * Name of GeoIp Test Group
     */
    const GEOIPTEST_GROUP = 'geoiptest';

    /**
     * Local Path
     */
    const LOCAL_PATH = 'prgeoiplookup/data/';

    /**
     * Name of GeoIp Test Group
     */
    const NEKUDO_CONNECTION_URL = 'geoip.nekudo.com/api';

    /**
     * IpToCountry source file on Wiki
     */
    const IPTOCOUNTRY_SOURCE_FILE = 'http://wiki.plumrocket.com/data/geoip/IpToCountry.csv';

    /**
     * Version source file on Wiki
     */
    const PATH_VERSION_FILE  = 'http://wiki.plumrocket.com/data/geoip/versions.csv';

    /**
     * Maxmind source file on Wiki
     */
    const PATH_MAXMIND_BLOCKS = 'http://wiki.plumrocket.com/data/geoip/GeoLite2-City-Blocks-IPv4.csv';

    /**
     * Maxmind source file on Wiki
     */
    const PATH_MAXMIND_LOCATIONS = 'http://wiki.plumrocket.com/data/geoip/GeoLite2-City-Locations-en.csv';

    /**
     * Retrieve config value according to current section identifier
     *
     * @param string $path
     * @param string|int $store
     * @return mixed
     */
    private function getConfigForCurrentSection($path, $store = null)
    {
        return $this->getConfig(
            Data::SECTION_ID  . '/'. $path,
            $store
        );
    }

    /**
     * @param int|string $store
     * @return bool
     */
    public function enabledMaxmindGeoIp($store = null)
    {
        return (bool)$this->getConfigForCurrentSection(
            'methods/' . self::MAXMIND_GROUP . '/enabled',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getMaxmindInstallMethod($store = null)
    {
        return $this->getConfigForCurrentSection(
            'methods/' . self::MAXMIND_GROUP . '/install_method',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return bool
     */
    public function enabledIpToCountryGeoIp($store = null)
    {
        return (bool)$this->getConfigForCurrentSection(
            'methods/' . self::IPTOCOUNTRY_GROUP . '/enabled',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return string
     */
    public function getIpToCountryInstallMethod($store = null)
    {
        return $this->getConfigForCurrentSection(
            'methods/' . self::IPTOCOUNTRY_GROUP . '/install_method',
            $store
        );
    }

    /**
     * @param int|string $store
     * @return bool
     */
    public function enabledNekudoGeoIp($store = null)
    {
        return (bool)$this->getConfigForCurrentSection(
            'methods/' . self::NEKUDO_GROUP . '/enabled',
            $store
        );
    }

    /**
     * @param null $store
     * @return int
     */
    public function getEnableMethodsNumber($store = null)
    {
        $methodsArray = [self::MAXMIND_GROUP, self::IPTOCOUNTRY_GROUP, self::NEKUDO_GROUP];
        $count = 0;
        foreach ($methodsArray as $method) {
            if ($this->getConfigForCurrentSection(
                'methods/' . $method . '/enabled',
                $store
            )) {
                $count++;
            }
        }

        return $count;
    }
}