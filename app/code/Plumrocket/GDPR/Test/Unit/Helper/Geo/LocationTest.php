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

namespace Plumrocket\GDPR\Test\Unit\Helper\Geo;

use Plumrocket\GDPR\Helper\Geo\Location as GeoLocationHelper;
use Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions;

class LocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider validateLocationProvider
     *
     * @param $counter
     * @param $options
     * @param $isInEU
     * @param $countryCode
     * @param $result
     */
    public function testValidateLocation($counter, $options, $isInEU, $countryCode, $result)
    {
        /** @var GeoLocationHelper $helper */
        $helper = $this->getMockBuilder(GeoLocationHelper::class)
            ->disableOriginalConstructor()
            ->setMethodsExcept([
                'validateLocation'
            ])->getMock();

        $getGeoIpInfo = function () use ($countryCode, $isInEU) {
            return [$countryCode, $isInEU];
        };

        $this->assertEquals(
            $result,
            $helper->validateLocation($options, $getGeoIpInfo),
            'Error ' . $counter . ' location validate!'
        );
    }

    /**
     * @return array[]
     */
    public function validateLocationProvider()
    {
        return [
            [
                'counter' => 1,
                'options' => [GeoIPRestrictions::ALL],
                'isInEU'  => false,
                'countryCode' => 'CA',
                'result'  => true,
            ],
            [
                'counter' => 2,
                'options' => [GeoIPRestrictions::EU, 'AU'],
                'isInEU'  => false,
                'countryCode' => 'CA',
                'result'  => false,
            ],
            [
                'counter' => 3,
                'options' => [GeoIPRestrictions::EU, 'AU'],
                'isInEU'  => false,
                'countryCode' => 'AU',
                'result'  => true,
            ],
            [
                'counter' => 4,
                'options' => [GeoIPRestrictions::EU, 'AU'],
                'isInEU'  => true,
                'countryCode' => 'DE',
                'result'  => true,
            ],
            [
                'counter' => 5,
                'options' => [GeoIPRestrictions::EU, 'AU'],
                'isInEU'  => null,
                'countryCode' => null,
                'result'  => false,
            ],
            [
                'counter' => 7,
                'options' => ['PL', 'AU'],
                'isInEU'  => true,
                'countryCode' => 'FR',
                'result'  => false,
            ],
            [
                'counter' => 8,
                'options' => [GeoIPRestrictions::UNKNOWN],
                'isInEU'  => true,
                'countryCode' => 'FR',
                'result'  => false,
            ],
            [
                'counter' => 9,
                'options' => [GeoIPRestrictions::UNKNOWN],
                'isInEU'  => null,
                'countryCode' => null,
                'result'  => true,
            ],
            [
                'counter' => 10,
                'options' => [],
                'isInEU'  => true,
                'countryCode' => 'FR',
                'result'  => true,
            ],
        ];
    }
}
