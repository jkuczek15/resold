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

use Magento\Config\Model\Config\Source\Yesno;
use Plumrocket\GDPR\Model\Config\Source\Pages;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;
use Plumrocket\GDPR\Model\Config\Source\GeoIPRestrictions;
use Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable;
use Magento\Backend\Block\Widget\Button as ButtonWidget;

/**
 * Class Checkboxes
 */
class Checkboxes extends \Magento\Config\Block\System\Config\Form\Field
{
    const DEVELOPERS_GUIDE_URL = 'http://wiki.plumrocket.com/Magento_2_GDPR_v1.x_Developers_Guide_and_API_Reference';

    /**
     * @var Yesno
     */
    private $yesno;

    /**
     * @var Pages
     */
    private $pages;

    /**
     * @var ConsentLocations
     */
    private $consentLocations;

    /**
     * @var GeoIPRestrictions
     */
    private $geoIPRestrictions;

    /**
     * @param Yesno $yesno
     * @param Pages $pages
     * @param ConsentLocations $consentLocations
     * @param GeoIPRestrictions $geoIPRestrictions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Yesno $yesno,
        Pages $pages,
        ConsentLocations $consentLocations,
        GeoIPRestrictions $geoIPRestrictions,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->yesno = $yesno;
        $this->pages = $pages;
        $this->consentLocations = $consentLocations;
        $this->geoIPRestrictions = $geoIPRestrictions;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.
        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        $html = $this->_renderValue($element);

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    protected function _getElementHtml(// @codingStandardsIgnoreLine we need to extend parent method
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $html = '<input type="hidden" name="'. $element->getName() .'" value="" />';

        /** @var InputTable $inputTableBlock */
        $inputTableBlock = $this->getLayout()->createBlock(InputTable::class);

        $inputTableBlock->setTemplate('Plumrocket_GDPR::widget/grid/extended.phtml');

        if (! $inputTableBlock) {
            throw new \Magento\Framework\Exception\LocalizedException(__(
                'Block %1 not found.',
                InputTable::class
            ));
        }

        $inputTableBlock->setContainerFieldId($element->getName())->setRowKey('name');
        $inputTableBlock->addColumn('page_type', [
                'header'    => __('Consent Location'),
                'index'     => 'page_type',
                'type'      => 'select',
                'options'   => $this->consentLocations->toOptionAssocArray(true),
                'value'     => 1,
                'column_css_class' => 'page-type',
            ])->addColumn('checkbox_label', [
                'header'    => __('Checkbox Label'),
                'index'     => 'checkbox_label',
                'type'      => 'input',
                'value'     => '',
                'column_css_class' => 'checkbox-label',
            ])->addColumn('page_id', [
                'header'    => __('Link to CMS Page'),
                'index'     => 'page_id',
                'type'      => 'select',
                'options'   => $this->pages->toOptionArray(),
                'value'     => 0,
                'column_css_class' => 'page-link',
            ])->addColumn('is_required', [
                'header'    => __('Required'),
                'index'     => 'is_required',
                'type'      => 'select',
                'value'     => 1,
                'options'   => $this->yesno->toArray(),
                'column_css_class' => 'page-link',
            ])->addColumn('remove', [
                'header'    => __('Action'),
                'index'     => 'remove',
                'type'      => 'text',
                'renderer'  => 'Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\Button',
                'value'     => 1,
                'column_css_class' => 'remove',
                'rowspan'  => 2,
            ])->addGeoIp('geo_ip_restrictions', [
                'index' => 'geo_ip_restrictions',
            ])->setArray(
                $this->_getValue($element->getValue())
            );

        $html .= $inputTableBlock->toHtml();
        $html .= $this->getLayout()
            ->createBlock(ButtonWidget::class)
            ->addData([
                'label'   => __('+ Add New'),
                'type'    => 'button',
                'class'   => 'add checkbox-add action-secondary',
            ])->toHtml();

        $consentLogUrl = $this->getUrl('prgdpr/consentslog/index');
        // @codingStandardsIgnoreStart
        $comment = [
            __('Add checkboxes (such as "I agree to the Privacy Policy" or "I agree to the TOS") in multiple locations of your store. You can also add consent checkboxes manually using our <a target="_blank" href="%1">developer\'s guide</a>.', self::DEVELOPERS_GUIDE_URL),
            __('All customers who agreed to these policies can be tracked via <a target="_blank" href="%1">consent log</a>.', $consentLogUrl),
        ];
        // @codingStandardsIgnoreEnd

        $commentHtml = '<p class="note"><span>' . implode(' ', $comment) . '</span></p>';

        return $html . $commentHtml;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function _getValue($data = [])// @codingStandardsIgnoreLine we need to extend parent method
    {
        $rows = [
            '_TMPNAME_' => [],
        ];

        if ($data && is_array($data)) {
            $rows = array_merge($rows, $data);
        }

        foreach ($rows as $name => &$row) {
            $row = array_merge($row, [
                'name'      => $name,
            ]);

            if (isset($row['checkbox_label'])) {
                $row['checkbox_label'] = htmlspecialchars((string)$row['checkbox_label'], ENT_QUOTES);
            } else {
                $row['checkbox_label'] = htmlspecialchars(
                    'I agree to the <a href="{{url}}" class="pr-inpopup">Privacy Policy</a>',
                    ENT_QUOTES
                );
            }

            if (! isset($row['is_required'])) {
                $row['is_required'] = 1;
            }

            if (! isset($row['geo_ip_restrictions'])) {
                $row['geo_ip_restrictions'] = GeoIPRestrictions::ALL;
            }
        }

        return $rows;
    }
}
