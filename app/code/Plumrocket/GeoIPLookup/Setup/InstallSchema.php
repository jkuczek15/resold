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

namespace Plumrocket\GeoIPLookup\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Plumrocket\GeoIPLookup\Model\ResourceModel\InstalledVersions;
use Plumrocket\GeoIPLookup\Model\ResourceModel\IpToCountry;
use Plumrocket\GeoIPLookup\Model\ResourceModel\Maxmind;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         * InstalledVersions Table
         */
        $installedVersionsTable = $installer->getConnection()->newTable(
            $installer->getTable(InstalledVersions::MAIN_TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Record Identifier'
        )->addColumn(
            'data_name',
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => true
            ],
            'Database Name'
        )->addColumn(
            'file_version',
            Table::TYPE_TEXT,
            15,
            [
                'nullable' => true
            ],
            'Database File Version'
        )->addColumn(
            'installed_date',
            Table::TYPE_DATETIME,
            null,
            [
                'nullable' => true
            ],
            'Installation Date'
        )->setComment('Installed Versions');

        $installer->getConnection()->createTable($installedVersionsTable);

        /**
         * IpToCountry Table
         */
        $ipToCountryTable = $installer->getConnection()->newTable(
            $installer->getTable(IpToCountry::MAIN_TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Record Identifier'
        )->addColumn(
            'ip_from',
            Table::TYPE_INTEGER,
            11,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'IP From'
        )->addColumn(
            'ip_to',
            Table::TYPE_INTEGER,
            11,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'IP To'
        )->addColumn(
            'country_iso_code2',
            Table::TYPE_TEXT,
            2,
            [
                'nullable' => true
            ],
            'Country Code (ISO 3166-1 alpha-2)'
        )->addColumn(
            'country_iso_code3',
            Table::TYPE_TEXT,
            3,
            [
                'nullable' => true
            ],
            'Country Code (ISO 3166-1 alpha-3)'
        )->addColumn(
            'country_name',
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => true
            ],
            'Country Name'
        )->setComment('IpToCountry Geo Ip Country Table');

        $installer->getConnection()->createTable($ipToCountryTable);

        /**
         * Maxmind GeoIp City Blocks Table
         */
        $maxmindGeoIpCityBlocks = $installer->getConnection()->newTable(
            $installer->getTable(Maxmind::MAIN_TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Record Identifier'
        )->addColumn(
            'ip_from',
            Table::TYPE_INTEGER,
            11,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'IP From'
        )->addColumn(
            'ip_to',
            Table::TYPE_INTEGER,
            11,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'IP To'
        )->addColumn(
            'location_id',
            Table::TYPE_INTEGER,
            11,
            [
                'unsigned' => true,
                'nullable' => false,
            ],
            'Location Id'
        )->addColumn(
            'postal_code',
            Table::TYPE_TEXT,
            25,
            [
                'nullable' => true
            ],
            'Postal Code'
        )->addColumn(
            'latitude',
            Table::TYPE_TEXT,
            25,
            [
                'nullable' => true
            ],
            'Latitude'
        )->addColumn(
            'longitude',
            Table::TYPE_TEXT,
            25,
            [
                'nullable' => true
            ],
            'Longitude'
        )->setComment('Maxmindgeoip GeoLite2-City-Blocks');

        $installer->getConnection()->createTable($maxmindGeoIpCityBlocks);

        /**
         * Maxmind Geoip City Locations Table
         */
        $maxmindGeoipCityLocations = $installer->getConnection()->newTable(
            $installer->getTable(Maxmind::SECONDARY_TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Record Identifier'
        )->addColumn(
            'locale_code',
            Table::TYPE_TEXT,
            2,
            [
                'nullable' => false,
            ],
            'Locale Code'
        )->addColumn(
            'continent_code',
            Table::TYPE_TEXT,
            2,
            [
                'nullable' => false,
            ],
            'Continent Code'
        )->addColumn(
            'continent_name',
            Table::TYPE_TEXT,
            50,
            [
                'nullable' => false,
            ],
            'Continent Name'
        )->addColumn(
            'country_iso_code2',
            Table::TYPE_TEXT,
            2,
            [
                'nullable' => true
            ],
            'Country Code (ISO 3166-1 alpha-2)'
        )->addColumn(
            'country_name',
            Table::TYPE_TEXT,
            255,
            [
                'nullable' => true
            ],
            'Country Name'
        )->addColumn(
            'subdivision_1_iso_code',
            Table::TYPE_TEXT,
            5,
            [
                'nullable' => true
            ],
            'Subdivision 1 (ISO Code)'
        )->addColumn(
            'subdivision_1_name',
            Table::TYPE_TEXT,
            25,
            [
                'nullable' => true,
            ],
            'Subdivision 1 Name'
        )->addColumn(
            'subdivision_2_iso_code',
            Table::TYPE_TEXT,
            5,
            [
                'nullable' => true,
            ],
            'Subdivision 2 (ISO Code)'
        )->addColumn(
            'subdivision_2_name',
            Table::TYPE_TEXT,
            100,
            [
                'nullable' => true,
            ],
            'Subdivision 2 Name'
        )->addColumn(
            'city_name',
            Table::TYPE_TEXT,
            100,
            [
                'nullable' => false,
            ],
            'City Name'
        )->addColumn(
            'metro_code',
            Table::TYPE_TEXT,
            15,
            [
                'nullable' => true,
            ],
            'Area Code'
        )->addColumn(
            'time_zone',
            Table::TYPE_TEXT,
            100,
            [
                'nullable' => false,
            ],
            'Time Zone'
        )->addColumn(
            'is_in_european_union',
            Table::TYPE_INTEGER,
            1,
            [
                'nullable' => false,
            ],
            'Is in European Union'
        )->setComment('Maxmindgeoip GeoLite2-City-Locations');

        $installer->getConnection()->createTable($maxmindGeoipCityLocations);

        $installer->endSetup();
    }
}