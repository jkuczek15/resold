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

namespace Plumrocket\GDPR\Block\Adminhtml\System\Config\Form\Checkboxes;

class Button extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render( // @codingStandardsIgnoreLine argument $row must be specified
        \Magento\Framework\DataObject $row
    ) {
        return $this->getLayout()
                ->createBlock('Magento\Backend\Block\Widget\Button')
                ->addData([
                    'id'      => 'checkbox_remove_button',
                    'label'   => __('Delete'),
                    'type'    => 'button',
                    'class'   => 'delete checkbox-remove ' . $this->getColumn()->getId(),
                ])
                ->toHtml();
    }
}
