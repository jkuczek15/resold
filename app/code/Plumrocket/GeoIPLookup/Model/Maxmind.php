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

use Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind as MaxmindResource;

class Maxmind extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Data\Import\Maxmindgeoip
     */
    private $maxmindgeoip;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Data
     */
    private $dataHelper;

    /**
     * Maxmind constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param Data\Import\Maxmindgeoip                                     $maxmindgeoip
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress         $remoteAddress
     * @param \Plumrocket\GeoIPLookup\Helper\Data                          $dataHelper
     * @param \Magento\Framework\App\ResourceConnection                    $resourceConnection
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindgeoip,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Plumrocket\GeoIPLookup\Helper\Data $dataHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->maxmindgeoip = $maxmindgeoip;
        $this->resourceConnection = $resourceConnection;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct Maxmind Resouce
     */
    public function _construct()
    {
        $this->_init('\Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind');
    }

    /**
     * @param int $ip
     * @return mixed|null
     */
    public function getGeoLocation($ip = 0)
    {
        $result = null;
        if ($ip === 0) {
            $ip = $this->remoteAddress->getRemoteAddress();
        }

        if ($ip) {
            $ip = ip2long($ip);

            $cacheKey = $ip . '_gl_mx';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $result = $this->getCollection()
                ->addFieldToSelect('postal_code')
                ->addFieldToSelect('latitude')
                ->addFieldToSelect('longitude')
                ->addFieldToFilter('ip_from', ['lteq' => $ip])
                ->addFieldToFilter('ip_to', ['gteq' => $ip])
                ->join(
                    ['locations' => $this->resourceConnection->getTableName(
                        MaxmindResource::SECONDARY_TABLE_NAME
                    )],
                    'main_table.location_id = locations.entity_id',
                    [
                        'continent_code',
                        'continent_name',
                        'country_code' => 'country_iso_code2',
                        'country_name',
                        'subdivision_1_iso_code',
                        'subdivision_1_name',
                        'subdivision_2_iso_code',
                        'subdivision_2_name',
                        'city_name',
                        'metro_code',
                        'time_zone',
                        'is_in_european_union'
                    ]
                );

            $result->getSelect()->order('main_table.entity_id', 'ASC')->limit(1);
            $result = $result->getFirstItem()->getData();

            if (!empty($result)) {
                $result['database_name'] = $this->maxmindgeoip->dataName;
                $result['is_in_european_union'] = (bool)$result['is_in_european_union'];
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
            $ip = ip2long($ip);

            $cacheKey = $ip . '_cc_mx';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $result = $this->getCollection()
                ->addFieldToFilter('ip_from', ['lteq' => $ip])
                ->addFieldToFilter('ip_to', ['gteq' => $ip])
                ->join(
                    ['locations' => $this->resourceConnection->getTableName(
                        MaxmindResource::SECONDARY_TABLE_NAME
                    )],
                    'main_table.location_id = locations.entity_id',
                    ['country_iso_code2']
                )
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem()
                ->getData();

            $result = (!empty($result)) ? $result['country_iso_code2'] : null;
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
            $cacheKey = $countryCode . '_hc_mx';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $countryCode = strtoupper($countryCode);
            $db = $this->resourceConnection->getConnection(
                ResourceConnection::DEFAULT_CONNECTION
            );

            $select = $db->select()
                ->from(
                    ['main_table' => $this->resourceConnection->getTableName(
                        MaxmindResource::SECONDARY_TABLE_NAME
                    )],
                    ['country_iso_code2']
                )
                ->where('main_table.country_iso_code2 = ?', $countryCode)
                ->limit(0,1);
            $result = ($db->fetchOne($select)) ? true: false;

            $geoipCache[$cacheKey] = $result;
            $this->dataHelper->setGeoIpCache($geoipCache);
        }

        return $result;
    }
}