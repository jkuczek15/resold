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

namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form;

use Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions;
use Magento\Framework\View\Element\Html\Select as HtmlSelect;

class MultiSelect extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions
     */
    private $geoIPRestrictions;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * MultiSelect constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions $geoIPRestrictions
     * @param \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions $geoIPRestrictions,
        \Plumrocket\GDPR\Helper\Geo\Location $geoLocationHelper,
        array $data = []
    ) {
        $this->geoIPRestrictions = $geoIPRestrictions;
        $this->geoLocationHelper = $geoLocationHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->geoLocationHelper->getGeoIpRestrictionsNotice();
        $value = $element->getEscapedValue();
        $extraParams = ' multiple';

        if (! $this->geoLocationHelper->canUseGeoIP()) {
            $extraParams .= ' readonly="readonly"';
            $value = GeoIPRestrictions::ALL;
        }

        /** @var HtmlSelect $select */
        $select = $this->getLayout()->createBlock(HtmlSelect::class);
        $select->setOptions($this->geoIPRestrictions->toOptionArray());
        $select->setId($element->getId());
        $select->setName($element->getName());
        $select->setValue(explode(',', $value));
        $select->setExtraParams($extraParams);
        $select->setClass($element->getClass());
        $html .= $select->toHtml();

        return $html;
    }
}
