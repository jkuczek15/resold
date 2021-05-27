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

namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes;

use Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable\Column;
use Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable\GeoIp as GeoIpBlock;
use Magento\Framework\Exception\LocalizedException;

class InputTable extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const HIDDEN_ELEMENT_CLASS = 'hidden-input-table';

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected $element;

    /**
     * @var null | string
     */
    protected $containerFieldId = null;

    /**
     * @var null | string
     */
    protected $rowKey = null;

    /**
     * @var \Magento\Framework\Data\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var GeoIpBlock
     */
    protected $geoIpBlock = null;

    /**
     * InputTable constructor.
     *
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Backend\Helper\Data              $backendHelper
     * @param \Magento\Framework\Data\CollectionFactory $collectionFactory
     * @param \Magento\Framework\DataObjectFactory      $dataObjectFactory
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Data\CollectionFactory $collectionFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
    }

    // ******************************************
    // *                                        *
    // *           Grid functions               *
    // *                                        *
    // ******************************************
    public function _construct()
    {
        parent::_construct();
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setMessageBlockVisibility(false);
    }

    /**
     * @param string                              $columnId
     * @param array|\Magento\Framework\DataObject $column
     *
     * @return $this
     * @throws LocalizedException
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $this->getColumnSet()->setChild(
                $columnId,
                $this->getLayout()
                    ->createBlock(Column::class)
                    ->setData($column)
                    ->setId($columnId)
                    ->setGrid($this)
            );
            $this->getColumnSet()->getChildBlock($columnId)->setGrid($this);
        } else {
            throw new LocalizedException(__('Please correct the column format and try again.'));
        }

        $this->_lastColumnId = $columnId;
        return $this;
    }

    /**
     * @param $columnId
     * @param $column
     * @return $this
     * @throws LocalizedException
     */
    public function addGeoIp($columnId, $column)
    {
        if (! $this->getContainerFieldId() || ! $this->getRowKey()) {
            throw new LocalizedException(__('Container Field Id and Row Key must be set.'));
        } elseif (is_array($column)) {
            $column['sortable'] = false;

            $this->geoIpBlock = $this->getLayout()
                ->createBlock(GeoIpBlock::class)
                ->setData($column)
                ->setGrid($this);
        } else {
            throw new LocalizedException(__('Wrong column format.'));
        }

        $this->geoIpBlock->setId($columnId);

        return $this;
    }

    /**
     * @param $item
     * @return string
     */
    public function getGeoIpHtml($item)
    {
        return $this->geoIpBlock->setCheckboxDataObject($item)->toHtml();
    }

    /**
     * @return bool
     */
    public function canDisplayContainer()
    {
        return false;
    }

    /**
     * @return \Magento\Backend\Block\Widget|\Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareLayout()
    {
        return \Magento\Backend\Block\Widget::_prepareLayout();
    }

    public function setArray($array)
    {
        $collection = $this->collectionFactory->create();
        $i = 1;
        foreach ($array as $item) {
            if (! $item instanceof \Magento\Framework\DataObject) {
                $item = $this->dataObjectFactory->create(['data' => $item]);
            }
            if (!$item->getId()) {
                $item->setId($i);
            }
            $collection->addItem($item);
            $i++;
        }
        $this->setCollection($collection);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRowKey()
    {
        return $this->rowKey;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setRowKey($key)
    {
        $this->rowKey = $key;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getContainerFieldId()
    {
        return $this->containerFieldId;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setContainerFieldId($name)
    {
        $this->containerFieldId = $name;
        return $this;
    }

    // ******************************************
    // *                                        *
    // *           Render functions             *
    // *                                        *
    // ******************************************

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return '
            <tr>
                <td class="label">' . $element->getLabelHtml() . '</td>
                <td class="value">' . $this->toHtml() . '</td>
            </tr>';
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return $this
     */
    public function setElement(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->element = $element;
        return $this;
    }

    /**
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return mixed|null|string|string[]
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        $html = preg_replace(
            '/(\s+class\s*=\s*["\'](?:\s*|[^"\']*\s+)messages)((?:\s*|\s+[^"\']*)["\'])/isU',
            '$1 ' . self::HIDDEN_ELEMENT_CLASS . ' $2',
            $html
        );
        $html = str_replace(
            '<div class="admin__data-grid-wrap',
            '<div id="' . $this->getHtmlId() . '_wrap" class="admin__data-grid-wrap',
            $html
        );
        $html .= $this->_getCss();

        return $html;
    }

    /**
     * @return string
     */
    private function _getCss()
    {
        $id = '#' . $this->getHtmlId() . '_wrap';

        return "<style>
            .messages." . self::HIDDEN_ELEMENT_CLASS . "{display:none}
            $id {
                margin-bottom: 0;
                padding-bottom: 0;
                padding-top: 0;
            }
            $id td {
                padding: 1rem;
                vertical-align: middle;
            }
            $id td input.checkbox[disabled] {
                display: none;
            }
            $id tr.not-active td,
            $id tr.not-active input.input-text {
                color: #999999;
            }
        </style>";
    }
}
