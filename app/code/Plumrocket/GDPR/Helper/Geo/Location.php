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

namespace Plumrocket\GDPR\Helper\Geo;

use Plumrocket\GDPR\Helper\Data as DataHelper;
use Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions;

/**
 * Class Location
 */
class Location extends \Plumrocket\GDPR\Helper\Main
{
    /**
     * Name of Geo IP module
     */
    const GEO_IP_MODULE_NAME = 'GeoIPLookup';

    /**
     * Geo IP Module key
     */
    const GEO_IP_MODULE_KEY = 'prgeoiplookup';

    /**
     * Geo IP model class
     */
    const GEO_IP_MODEL_PATH = '\Plumrocket\GeoIPLookup\Model\GeoIPLookup';

    /**
     * Geo IP Helper class
     */
    const GEO_IP_HELPER_PATH = '\Plumrocket\GeoIPLookup\Helper\Config';

    /**
     * Name of method for GeoIPLookup Helper that retrieving GeoIP method identifier
     */
    const GEO_IP_HELPER_METHOD_NAME = 'getEnableMethodsNumber';

    /**
     * @var string
     */
    private $geoIpModuleName = self::GEO_IP_MODULE_NAME;

    /**
     * @var string
     */
    public $geoIpModuleKey = self::GEO_IP_MODULE_KEY;

    /**
     * @var string
     */
    public $geoIpModelPath = self::GEO_IP_MODEL_PATH;

    /**
     * @var string
     */
    public $geoIpHelperPath = self::GEO_IP_HELPER_PATH;

    /**
     * @var string
     */
    public $geoIpHelperMethodName = self::GEO_IP_HELPER_METHOD_NAME;

    /**
     * @var array
     */
    private $requiredMethods = [
        'isInEuropeanUnion',
        'getCountryCode',
    ];

    /**
     * @var bool|null
     */
    private $canUseGeoIP;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * Location constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->backendUrl = $backendUrl;
        parent::__construct($objectManager, $context);
    }

    /**
     * @return bool
     */
    private function isGeoIpModuleExists()
    {
        return false !== $this->moduleExists($this->geoIpModuleName);
    }

    /**
     * @return bool
     */
    private function isGeoIpModuleEnabled()
    {
        return 2 == $this->moduleExists($this->geoIpModuleName);
    }

    /**
     * @return null|\Plumrocket\GeoIPLookup\Model\GeoLocation
     */
    private function getGeoIpModel()
    {
        return $this->isGeoIpModuleEnabled()
            ? $this->_objectManager->create($this->geoIpModelPath)
            : null;
    }

    /**
     * @return null|\Plumrocket\GeoIPLookup\Helper\Data
     */
    private function getGeoIpHelper()
    {
        return $this->isGeoIpModuleEnabled()
            ? $this->_objectManager->create($this->geoIpHelperPath)
            : null;
    }

    /**
     * @return bool
     */
    public function canUseGeoIP()
    {
        if (null === $this->canUseGeoIP) {
            $this->canUseGeoIP = $this->isGeoIpModuleEnabled()
                && $this->isValidLocationModel()
                && (0 !== $this->getGeoIpMethodIdentifier());
        }

        return $this->canUseGeoIP;
    }

    /**
     * @return bool
     */
    public function getGeoIpMethodIdentifier()
    {
        $geoIpHelper = $this->getGeoIpHelper();
        $methodName = $this->geoIpHelperMethodName;

        if (! $geoIpHelper || ! method_exists($geoIpHelper, $methodName)) {
            return false;
        }

        return $geoIpHelper->$methodName();
    }

    /**
     * @return bool
     */
    private function isValidLocationModel()
    {
        $model = $this->getGeoIpModel();

        if (! $model) {
            return false;
        }

        foreach ($this->requiredMethods as $method) {
            if (! method_exists($model, $method)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isPassCookieGeoIPRestriction()
    {
        if (! $this->canUseGeoIP()) {
            return true;
        }

        $geoIpModel = $this->getGeoIpModel();

        if (is_object($geoIpModel)) {
            return $this->validateLocation(
                $this->getCookieGeoIPRestriction(),
                function () use ($geoIpModel) {
                    return [
                        $geoIpModel->getCountryCode(),
                        $geoIpModel->isInEuropeanUnion(),
                    ];
                }
            );
        }

        return false;
    }

    /**
     * @param $checkboxData
     * @return bool
     */
    public function isPassCheckboxGeoIPRestriction($checkboxData)
    {
        if (! $this->canUseGeoIP()) {
            return true;
        }

        $restrictions = [];

        if (! empty($checkboxData['geo_ip_restrictions'])
            && is_array($checkboxData['geo_ip_restrictions'])
        ) {
            foreach ($checkboxData['geo_ip_restrictions'] as $item) {
                if (is_array($item)) {
                    $restrictions = array_merge($restrictions, array_values($item));
                } else {
                    array_push($restrictions, $item);
                }
            }

            $restrictions = array_unique($restrictions);
        }

        $geoIpModel = $this->getGeoIpModel();

        return $this->validateLocation(
            $restrictions,
            function () use ($geoIpModel) {
                return [
                    $geoIpModel->getCountryCode(),
                    $geoIpModel->isInEuropeanUnion(),
                ];
            }
        );
    }

    /**
     * @param $options
     * @param callable $getGeoIpInfo
     * @return bool
     */
    public function validateLocation($options, callable $getGeoIpInfo)
    {
        if (empty($options)
            || in_array(GeoIPRestrictions::ALL, $options)
        ) {
            return true;
        }

        list ($countryCode, $isInEU) = $getGeoIpInfo();

        if ($countryCode) {
            if (in_array(GeoIPRestrictions::EU, $options) && $isInEU) {
                return true;
            }

            if (in_array($countryCode, $options)) {
                return true;
            }
        } else {
            if (in_array(GeoIPRestrictions::UNKNOWN, $options)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve config values
     *
     * @param int|string $store
     * @return array
     */
    public function getCookieGeoIPRestriction($store = null)
    {
        $result = [];
        $configValue = $this->getConfig(DataHelper::SECTION_ID . '/cookie_consent/geoip_restriction', $store);

        if (! empty($configValue)) {
            $configData = explode(',', $configValue);

            if (is_array($configData)) {
                $result = array_merge($result, $configData);
            }
        }

        return $result;
    }

    public function getGeoIpRestrictionsNotice($withContainer = true)
    {
        $href = $this->backendUrl->getUrl('adminhtml/system_config/edit', [
            'section' => $this->geoIpModuleKey,
        ]);
        $message = '';

        switch (true) {
            case ! $this->isGeoIpModuleExists():
                $message = __(
                    'The GDPR Geo Targeting is disabled. You must install Plumrocket GeoIP Lookup extension to enable this feature.'
                );
                break;

            case ! $this->isGeoIpModuleEnabled():
                $message = __(
                    'The GDPR Geo Targeting is disabled. Click <a target="_blank" href="%1">here</a> to open Plumrocket GeoIP Lookup configuration and enable the GeoIP extension.',
                    $href
                );
                break;

            case ! $this->getGeoIpMethodIdentifier():
                $message = __(
                    'Please enable at least one GeoIP Lookup database in order to use GDPR Geo Targeting. Click <a target="_blank" href="%1">here</a> to open Plumrocket GeoIP Lookup configuration and enable the GeoIP databases.',
                    $href
                );
                break;
        }

        return (bool)$withContainer && ! empty($message)
            ? $this->getPreparedNoticeHtml($message)
            : $message;
    }

    /**
     * @param $message
     * @return string
     */
    public function getPreparedNoticeHtml($message)
    {
        $attributes = [
            'style="color: #eb5202;background: none;font-size:12px"',
        ];

        return '<div ' . implode(' ', $attributes) . '><div><i>' . $message . '</i></div></div>';
    }
}
