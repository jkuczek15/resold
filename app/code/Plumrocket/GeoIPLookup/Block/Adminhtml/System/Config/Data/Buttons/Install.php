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

namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons;

class Install extends \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons\AbstractBlock
{
    /**
     * Button Label
     */
    public $buttonLabel = 'Install Database';

    /**
     * @return string
     */
    public function getOnclick($htmlId = null)
    {
        $serviceId = mb_substr($htmlId, 0, mb_strrpos($htmlId,"_"));

        $urlAutomatic = $this->getUrl(
            'prgeoiplookup/import/autoimport',
            ['dataId' => $serviceId]
        );

        $urlManual = $this->getUrl(
            'prgeoiplookup/import/manualimport',
            ['dataId' => $serviceId]
        );

        $urlProgress = $this->getUrl(
            'prgeoiplookup/import/importprogress',
            ['dataId' => $serviceId, 'is_progress' => 1]
        );

        return sprintf(
            'window.runInstallation(\'%s\', \'%s\', \'%s\', \'%s\'); return false;',
            $serviceId,
            $urlAutomatic,
            $urlManual,
            $urlProgress
        );
    }
}