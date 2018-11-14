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

namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable;

use Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions;
use Magento\Framework\View\Element\Html\Select as HtmlSelect;

/**
 * Class GeoIp
 *
 * @method $this setCheckboxDataObject($item)
 * @method \Magento\Framework\DataObject getCheckboxDataObject()
 */
class GeoIp extends \Magento\Backend\Block\Widget\Grid\Column\Extended
{
    /**
     * @var null
     */
    protected $rowKeyValue = null;

    /**
     * @var \Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions
     */
    private $geoIPRestrictions;

    /**
     * @var \Plumrocket\GDPR\Helper\Geo\Location
     */
    private $geoLocationHelper;

    /**
     * GeoIp constructor.
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
     * @return string
     */
    public function getId()
    {
        if (null === $this->getGrid()) {
            return parent::getId();
        }

        return $this->getName();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowField(\Magento\Framework\DataObject $row)
    {
        if (null !== $this->getGrid()->getRowKey()) {
            $this->rowKeyValue = $row->getData($this->getGrid()->getRowKey());
        }

        if (! $this->rowKeyValue) {
            return '';
        }

        return parent::getRowField($row);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getHtmlName()
    {
        return $this->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return sprintf(
            '%s[%s][%s]',
            $this->getGrid()->getContainerFieldId(),
            $this->rowKeyValue,
            parent::getId()
        );
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->geoLocationHelper->getGeoIpRestrictionsNotice();
        $checkboxDataObject = $this->getCheckboxDataObject();
        $this->rowKeyValue = $checkboxDataObject->getName();
        $value = $checkboxDataObject->getGeoIpRestrictions();
        $extraParams = ' multiple';

        if (! $this->geoLocationHelper->canUseGeoIP()) {
            $value = [GeoIPRestrictions::ALL];
            $extraParams .= ' readonly="readonly"';
        }

        /** @var HtmlSelect $select */
        $select = $this->getLayout()->createBlock(HtmlSelect::class);
        $select->setOptions($this->geoIPRestrictions->toOptionArray());
        $select->setName($this->getId() . '[]');
        $select->setId($this->getId());
        $select->setValue($value);
        $select->setExtraParams($extraParams);
        $select->setClass('geoip-select-with-chosen');
        $html .= $select->toHtml();

        return $html;
    }
}