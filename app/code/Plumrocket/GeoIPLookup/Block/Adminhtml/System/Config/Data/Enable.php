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

class Enable extends \Magento\Config\Block\System\Config\Form\Field
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
     * Enable constructor.
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
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $dataVersion = null;
        $elementId = $element->getId();
        $modelName = $this->dataHelper->getModelNameByElementId($elementId, false, true);
        $tooltip = '';

        switch ($modelName) {
            case Config::IPTOCOUNTRY_GROUP:
                $dataVersion = $this->ipToCountry->getInstalledVersion();
                break;
            case Config::MAXMIND_GROUP:
                $dataVersion = $this->maxmindGeoIp->getInstalledVersion();
                break;
        }

        if ($dataVersion === null) {
            $element->setDisabled('disabled');
            $form = $element->getForm();
            $serviceId = mb_substr($elementId, 0, mb_strrpos($elementId, "_"));
            $fieldset = $form->getElement($serviceId);
            $title = __('Please install %1 in order to enable this GeoIP method.', $fieldset->getLegend());
            $tooltip .= '<div id="' . $serviceId . '_tooltip" class="tooltip" style="margin: -27px -28px 0 0;">
                <span><span><img src="'  . $this->getViewFileUrl('Plumrocket_Base::images/error_msg_icon.gif')
                . '" style="margin-top: 2px;float: right;" /></span></span>';

            $tooltip .= '<div class="tooltip-content">' . $title . '</div></div>';
        }
        $html = '<td class="value with-tooltip">';
        $html .= $this->_getElementHtml($element) . $tooltip;
        $html .= '</td>';

        return $html;
    }
}
