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
use \Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind as MaxmindResource;

class Maxmindgeoip extends \Plumrocket\GeoIPLookup\Model\Data\Import\AbstractModel
{
    /**
     * @var int
     */
    public $steps = 2;

    /**
     * @var string
     */
    public $dataName = 'Maxmind';

    /**
     * @var string
     */
    public $dataLabel = 'Maxmind GeoIP Database';

    /**
     * @var array
     */
    public $configData = [
        1 => [
            'step' => 1,
            'pathSourceFile' => Config::PATH_MAXMIND_BLOCKS,
            'fileName' => 'GeoLite2-City-Blocks-IPv4.csv',
            'dataMapping' => [
                'ip_from' => 0,
                'ip_to' => 0,
                'location_id' => 1,
                'postal_code' => 6,
                'latitude' => 7,
                'longitude' => 8
            ],
            'tableName' => MaxmindResource::MAIN_TABLE_NAME,
        ],
        2 => [
            'step' => 2,
            'pathSourceFile' => Config::PATH_MAXMIND_LOCATIONS,
            'fileName' => 'GeoLite2-City-Locations-en.csv',
            'dataMapping' => [
                'entity_id' => 0,
                'locale_code' => 1,
                'continent_code' => 2,
                'continent_name' => 3,
                'country_iso_code2' => 4,
                'country_name' => 5,
                'subdivision_1_iso_code' => 6,
                'subdivision_1_name' => 7,
                'subdivision_2_iso_code' => 8,
                'subdivision_2_name' => 9,
                'city_name' => 10,
                'metro_code' => 11,
                'time_zone' => 12,
                'is_in_european_union' => 13
            ],
            'tableName' => MaxmindResource::SECONDARY_TABLE_NAME,
        ],
    ];

    /**
     * @param $item
     */
    private function setConfig($item)
    {
        $this->pathSourceFile = $item['pathSourceFile'];
        $this->fileName = $item['fileName'];
        $this->dataMapping = $item['dataMapping'];
        $this->tableName = $item['tableName'];
        $this->step = $item['step'];
    }

    /**
     * @return array|null
     */
    public function autoImportData()
    {
        $result = null;
        foreach ($this->configData as $item) {
            $this->setConfig($item);
            $result =  parent::autoImportData();
            if ($result['status'] == 'process' && $this->step < $this->steps) {
                continue;
            } else {
                break;
            }
        }

        if ($result['status'] == 'process') {
            foreach ($this->configData as $item) {
                $ioFile = $this->getIoFileModel();
                $fileName = $this->getDataPath() . $item['fileName'];
                $ioFile->rm($fileName);
            }
        }

        return $result;
    }

    /**
     * @return array|bool|null
     */
    public function manualImportData()
    {
        $result = null;
        $filesExist = false;
        $ioFile = $this->getIoFileModel();
        foreach ($this->configData as $item) {
            $fileName = $this->getDataPath() . $item['fileName'];
            $isDataFileValid = $this->isDataFileValid($fileName);
            if ($isDataFileValid === true) {
                $filesExist = true;
            } else {
                $result = $isDataFileValid;
                break;
            }
        }

        if ($filesExist) {
            foreach ($this->configData as $item) {
                $this->setConfig($item);
                $result =  parent::manualImportData();
                if ($result['status'] == 'process' && $this->step < $this->steps) {
                    continue;
                } else {
                    break;
                }
            }

            if ($result['status'] == 'process') {
                foreach ($this->configData as $item) {
                    $fileName = $this->getDataPath() . $item['fileName'];
                    $ioFile->rm($fileName);
                }
            }
        }

        return $result;
    }

    /**
     * @param $key
     * @param $value
     * @return int|mixed
     */
    public function dataMapping($key, $value)
    {
        $ipFrom = 0;
        $ipTo = 0;
        if ($key == 'ip_from' || $key == 'ip_to') {
            $valueExp = explode('/', $value);
            $ip = $valueExp[0];
            $mask = $valueExp[1];
            $ip2long = ip2long($ip);
            $ipFrom = ($ip2long >> (32 - $mask))  << (32 - $mask);
            $ipTo = $ip2long | ~(-1 << (32 - $mask));
        }

        if ($key == 'ip_from') {
            $value = $ipFrom;
        } elseif ($key == 'ip_to') {
            $value = $ipTo;
        }

        return $value;
    }
}