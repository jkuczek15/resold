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

class Api extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Rest Api Static Path
     */
    const REST_PATH = 'rest/V1/prgeoiplookup/';

    /**
     * Api constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<p id="hidden_field_api_url">' . $this->getRestApiUrl() . '</p>'
            . '<a id="rest_ip_test" target="_blank" href=""></a>';

        return $html;
    }

    private function getRestApiUrl()
    {
        return $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . self::REST_PATH;
    }
}