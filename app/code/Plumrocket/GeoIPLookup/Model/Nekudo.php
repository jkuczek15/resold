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

namespace Plumrocket\GeoIPLookup\Model;

use Plumrocket\GeoIPLookup\Helper\Config;

class Nekudo
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * @var string
     */
    private $dataName = 'Geoip.Nekudo.Com';

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    private $dataHelper;

    /**
     * Nekudo constructor.
     *
     * @param \Magento\Framework\HTTP\Client\Curl                  $curl
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Json\Helper\Data                  $jsonHelper
     * @param \Plumrocket\GeoIPLookup\Helper\Data                  $dataHelper
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Plumrocket\GeoIPLookup\Helper\Data $dataHelper
    ) {
        $this->curl = $curl;
        $this->remoteAddress = $remoteAddress;
        $this->jsonHelper = $jsonHelper;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param int $ip
     * @return array|null
     */
    public function getGeoLocation($ip = 0)
    {
        $result = null;
        if ($ip === 0) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        if ($ip) {
            $cacheKey = ip2long($ip) . '_gl_ndo';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }
            $curlResult = $this->curlExec($ip);
            if ($curlResult) {
                $curlResultArr = $this->jsonHelper->jsonDecode($curlResult);
                if (isset($curlResultArr['error']) && $curlResultArr['error'] === 'error') {
                    return $result;
                } else {
                    $result = [
                        'country_code'  => isset($curlResultArr['country']['code'])
                            ? $curlResultArr['country']['code']
                            : '',
                        'country_name'  => isset($curlResultArr['country']['name'])
                            ? $curlResultArr['country']['name']
                            : '',
                        'city_name'     => isset($curlResultArr['city'])
                            ? $curlResultArr['city']
                            : '',
                        'latitude'      => isset($curlResultArr['location']['latitude'])
                            ? $curlResultArr['location']['latitude']
                            : '',
                        'longitude'     => isset($curlResultArr['location']['longitude'])
                            ? $curlResultArr['location']['longitude']
                            : '',
                        'time_zone'     => isset($curlResultArr['location']['time_zone'])
                            ? $curlResultArr['location']['time_zone']
                            : '',
                        'database_name' => $this->dataName
                    ];
                }
            }

            $result = (!empty($result)) ? $result : null;
            $geoipCache[$cacheKey] = $result;
            $this->dataHelper->setGeoIpCache($geoipCache);
        }

        return $result;
    }

    /**
     * @param int $ip
     * @return null
     */
    public function getCountryCode($ip = 0)
    {
        $result = null;
        if ($ip === 0) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        if ($ip) {
            $cacheKey = ip2long($ip) . '_cc_ndo';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }
            $curlResult = $this->curlExec($ip);
            if ($curlResult) {
                $curlResultArr = $this->jsonHelper->jsonDecode($curlResult);
                if (isset($curlResultArr['error']) && $curlResultArr['error'] === 'error') {
                    return $result;
                } else {
                    $result = $curlResultArr['country']['code'];
                }
            }
            $result = (!empty($result)) ? $result : null;
            $geoipCache[$cacheKey] = $result;
            $this->dataHelper->setGeoIpCache($geoipCache);
        }

        return $result;
    }

    /**
     * @param $countryCode
     * @return bool
     */
    public function hasCountry($countryCode)
    {
        $result = false;
        if ($countryCode) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param $ip
     * @return string
     */
    public function curlExec($ip)
    {
        $ip = ip2long($ip);
        $url = "http://" . Config::NEKUDO_CONNECTION_URL . '/' . $ip;
        $options = [
            CURLOPT_HEADER => 0,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
        ];
        $this->curl->post($url, $options);

        return $this->curl->getBody();
    }

}