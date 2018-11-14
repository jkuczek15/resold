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

namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Test;

use Plumrocket\GeoIPLookup\Helper\Config;

class Button extends \Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data\Buttons\AbstractBlock
{
    /**
     * Button Label
     */
    public $buttonLabel = 'Get Location From IP';

    /**
     * @param null $htmlId
     * @return string
     */
    public function getOnclick($htmlId = null)
    {
        $urlTest = $this->getUrl(
            'prgeoiplookup/test/index'
        );

        return sprintf(
            'window.geoipTest(\'%s\'); return false;',
            $urlTest
        );
    }

}