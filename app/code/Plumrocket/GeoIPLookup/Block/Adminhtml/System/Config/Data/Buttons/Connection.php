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

use Plumrocket\GeoIPLookup\Helper\Config;

class Connection extends \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons\AbstractBlock
{
    /**
     * Button Label
     */
    public $buttonLabel = 'Test Connection';

    /**
     * Action url
     */
    private $actionUrl = Config::NEKUDO_CONNECTION_URL;

    /**
     * @param $htmlId
     * @return null|string
     */
    public function getOnclick($htmlId = null)
    {
        return sprintf(
            'window.prGeoIptestApiConnection(\'%s\', \'%s\'); return false;',
            $this->actionUrl,
            $htmlId
        );
    }
}