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

class IpToCountry extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var Data\Import\Iptocountry
     */
    private $iptocountry;

    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Data
     */
    private $dataHelper;

    /**
     * IpToCountry constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress         $remoteAddress
     * @param \Plumrocket\GeoIPLookup\Helper\Data                          $dataHelper
     * @param Data\Import\Iptocountry                                      $iptocountry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Plumrocket\GeoIPLookup\Helper\Data $dataHelper,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry $iptocountry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->iptocountry = $iptocountry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct IpToCountry Resouce
     */
    public function _construct()
    {
        $this->_init('\Plumrocket\GeoIPLookup\Model\ResourceModel\IpToCountry');
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

            $cacheKey = $ip . '_gl_itc';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $result = $this->getCollection()
                ->addFieldToSelect('country_iso_code2', 'country_code')
                ->addFieldToSelect('country_name')
                ->addFieldToFilter('ip_from', ['lteq' => $ip])
                ->addFieldToFilter('ip_to', ['gteq' => $ip]);

            $result->getSelect()->order('main_table.entity_id', 'ASC')->limit(1);
            $result = $result->getFirstItem()->getData();

            if (!empty($result)) {
                $result['database_name'] = $this->iptocountry->dataName;
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

            $cacheKey = $ip . '_cc_itc';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }

            $result = $this->getCollection()
                ->addFieldToSelect('country_iso_code2')
                ->addFieldToFilter('ip_from', ['lteq' => $ip])
                ->addFieldToFilter('ip_to', ['gteq' => $ip])
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
            $cacheKey = $countryCode . '_hc_itc';
            $geoipCache = $this->dataHelper->getGeoIpCache();
            if (isset($geoipCache[$cacheKey])) {
                return $geoipCache[$cacheKey];
            }
            $countryCode = strtoupper($countryCode);
            $result = !$this->load($countryCode, 'country_iso_code2')->isEmpty();
            $geoipCache[$cacheKey] = $result;
            $this->dataHelper->setGeoIpCache($geoipCache);
        }

        return $result;
    }
}