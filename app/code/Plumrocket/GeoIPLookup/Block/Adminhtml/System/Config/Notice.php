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

namespace Plumrocket\GeoIPLookup\Block\Adminhtml\System\Config;

class Notice extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Plumrocket\GeoIPLookup\Helper\Config
     */
    private $config;

    /**
     * Notice constructor.
     *
     * @param \Plumrocket\GeoIPLookup\Helper\Config   $config
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array                                   $data
     */
    public function __construct(
        \Plumrocket\GeoIPLookup\Helper\Config $config,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $elementId = $element->getId();
        $html = '';
        $enableMethodsNumber = $this->config->getEnableMethodsNumber();

        if ($enableMethodsNumber < 2) {
            $text = __("Please Note!");
            if ($enableMethodsNumber == 0) {
                $text .= " " . __("You must enable at least one GeoIP Lookup 
                    method for this extension to function properly.
                ");
            }
            $text .= " " . __("We recommend to enable 2 or more GeoIP 
                methods below to provide the most accurate GeoLocation results.
            ");

            $html .= '
               <tr>
                   <td class="label" colspan="4" style="text-align:left;">
                       <div id="' . $elementId . '" class="prgeoiplookup-service-notice">' . $text . '</div>
                   </td>
               </tr>
            ';
        }

        return $html;
    }
}
