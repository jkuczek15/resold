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

namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config\Data;

use Plumrocket\GeoIPLookup\Helper\Config;

class Status extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry
     */
    private $ipToCountry;

    /**
     * @var \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip
     */
    private $maxmindGeoIp;

    /**
     * Status constructor.
     *
     * @param \Plumrocket\GeoIPLookup\Helper\Data                    $dataHelper
     * @param \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry  $ipToCountry
     * @param \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindGeoIp
     * @param \Magento\Backend\Block\Template\Context                $context
     * @param array                                                  $data
     */
    public function __construct(
        \Plumrocket\GeoIPLookup\Helper\Data $dataHelper,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Iptocountry $ipToCountry,
        \Plumrocket\GeoIPLookup\Model\Data\Import\Maxmindgeoip $maxmindGeoIp,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->ipToCountry = $ipToCountry;
        $this->maxmindGeoIp = $maxmindGeoIp;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $dataVersion = [];
        $elementId = $element->getId();
        $modelName = $this->dataHelper->getModelNameByElementId($elementId, false, true);

        switch ($modelName) {
            case Config::IPTOCOUNTRY_GROUP:
                $dataVersion = $this->ipToCountry->getInstalledVersion();
                break;
            case Config::MAXMIND_GROUP:
                $dataVersion = $this->maxmindGeoIp->getInstalledVersion();
                break;
        }

        $elementStyle =  ($dataVersion) ? "color: green" : "color: red";
        $elementText = $this->dataHelper->formatInstalledVersion($dataVersion);

        $html = '
            <div id="' . $elementId . '" class="status-container">
                <span class="status" style="' . $elementStyle . '">' . $elementText . '</span>
                <div class="progress-container" style="display: none;">
                    <div class="progress-bar w3-light-grey w3-border">
                        <div class="progress-value w3-container w3-blue w3-center" style="width:0%">0%</div>
                    </div>
                    <div class="progress-title"></div>
                </div>
            </div>
        ';

        return $html;
    }

}
