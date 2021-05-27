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
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes\InputTable;

class Column extends \Magento\Backend\Block\Widget\Grid\Column\Extended
{
    protected $rowKeyValue = null;

    public function getId()
    {
        $id = $this->getName();
        return $id;
    }

    public function getRowField(\Magento\Framework\DataObject $row)
    {
        if (null !== $this->getGrid()->getRowKey()) {
            $this->rowKeyValue = $row->getData($this->getGrid()->getRowKey());
        }
        if (!$this->rowKeyValue) {
            return '';
        }
        return parent::getRowField($row);
    }

    public function getFieldName()
    {
        return $this->getName();
    }

    public function getHtmlName()
    {
        return $this->getName();
    }

    public function getName()
    {
        return sprintf(
            '%s[%s][%s]',
            $this->getGrid()->getContainerFieldId(),
            $this->rowKeyValue,
            parent::getId()
        );
    }
}
