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

/**
 * Class Data Helper
 */
class Data extends \Plumrocket\GeoIPLookup\Helper\Main
{
    /**
     * Config section id
     */
    const SECTION_ID = 'prgeoiplookup';

    /**
     * @var string
     */
    protected $_configSectionId = self::SECTION_ID;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    private $configFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    private $sessionManager;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface            $objectManager
     * @param \Magento\Framework\App\Helper\Context                $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Config\Model\ConfigFactory                  $configFactory
     * @param \Magento\Framework\App\ResourceConnection            $resourceConnection
     * @param \Magento\Framework\Session\SessionManagerInterface   $sessionManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Config\Model\ConfigFactory $configFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager
    ) {
        parent::__construct($objectManager, $context);
        $this->timezone = $timezone;
        $this->configFactory = $configFactory;
        $this->resourceConnection = $resourceConnection;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param null $store
     * @return bool
     */
    public function moduleEnabled($store = null)
    {
        return (bool)$this->getConfig($this->_configSectionId . '/general/enabled', $store);
    }

    /**
     * @param $elementId
     * @return mixed
     */
    public function getModelNameByElementId($elementId, $upercase = true, $penultimate = false)
    {
        if ($penultimate) {
            $elementId = mb_substr($elementId, 0, mb_strrpos($elementId, "_"));
        }
        $elements = explode('_', $elementId);
        $modelName = end($elements);
        if ($upercase) {
            $modelName = ucfirst($modelName);
        }

        return $modelName;
    }

    /**
     * Disable Extension
     */
    public function disableExtension()
    {
        $config = $this->configFactory->create();
        $connection = $this->resourceConnection->getConnection('core_write');
        $connection->delete(
            $this->resourceConnection->getTableName('core_config_data'),
            [
                $connection->quoteInto('path = ?', $this->_configSectionId  . '/general/enabled')
            ]
        );
        $config->setDataByPath($this->_configSectionId  . '/general/enabled', 0);
        $config->save();
    }

    /**
     * @param $version
     * @return \Magento\Framework\Phrase
     */
    public function formatInstalledVersion($version)
    {
        $formattedVersion = __('Not Installed');
        if ($version) {
            $installedDate = $this->timezone->date($version['installed_date'])->format("F d, Y");
            if ($version['file_version']) {
                $formattedVersion = __("Installed v%1 on %2", $version['file_version'], $installedDate);
            } else {
                $formattedVersion = __("Installed on %1", $version);
            }
        }

        return $formattedVersion;
    }

    /**
     * @return mixed
     */
    public function getGeoIpCache()
    {
        $sessionData = $this->sessionManager->getPrGeoipData();
        $result = [];

        if (!empty($sessionData) && is_array($sessionData)) {
            $result = $sessionData;
        }

        return $result;
    }

    /**
     * @param $data
     */
    public function setGeoIpCache($data)
    {
        $this->sessionManager->setPrGeoipData($data);
    }
}