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

namespace Plumrocket\GDPR\Model\Config\Source;

class GeoIPRestrictions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * All site visitors
     */
    const ALL = 'all';

    /**
     * Only visitors from EU countries
     */
    const EU = 'eu';

    /**
     * Only visitors from unrecognized locations
     */
    const UNKNOWN = 'unknown';

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $directoryCountry;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * GeoIPRestrictions constructor.
     *
     * @param \Magento\Directory\Model\Config\Source\Country $directoryCountry
     * @param \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
     */
    public function __construct(
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
    ) {
        $this->directoryCountry = $directoryCountry;
        $this->geoLocationHelper = $geoLocationHelper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // All regions
        $regions = [
            [
                'value' => self::ALL,
                'label' => __('Show to all site visitors'),
                'params' => [],
            ],
            [
                'value' => self::EU,
                'label' => __('Only visitors from EU countries'),
            ],
            [
                'value' => self::UNKNOWN,
                'label' => __('Unknown'),
            ],
        ];

        // All countries
        $countries = $this->directoryCountry->toOptionArray(true);

        // Check if GeoIP Lookup exists and enabled
        if (! $this->geoLocationHelper->canUseGeoIP()) {
            $regions = array_map([$this, 'addDisabledAttribute'], $regions);
            $countries = array_map([$this, 'addDisabledAttribute'], $countries);
        }

        return [
            // Regions
            [
                'label' => __('By Region'),
                'value' => $regions,
            ],
            // Countries
            [
                'label' => __('By Country'),
                'value' => $countries,
            ],
        ];
    }

    /**
     * Return array of options
     *
     * @return array Format: array('value' => '<label>', ...)
     */
    public function toOptionAssocArray()
    {
        $assocOptions = [];
        $options = $this->toOptionArray();

        foreach ($options as $option) {
            $assocOptions[$option['value']] = $option['label'];
        }

        return $assocOptions;
    }

    public function addDisabledAttribute($option)
    {
        if (self::ALL != $option['value']) {
            $option['params']['disabled'] = 'disabled';
        }

        return $option;
    }
}
