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

class Cookie extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Backend\Helper\Data
     */
    private $helperBackend;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @todo we need to specified $request argument as a Proxy Class
     *
     * @param \Magento\Backend\Helper\Data $helperBackend
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $helperBackend,
        \Magento\Framework\App\Request\Http $request, // @codingStandardsIgnoreLine see docs
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->helperBackend = $helperBackend;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     * Argument $element must be specified
     * We need to extend parent method
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(// @codingStandardsIgnoreLine see docs
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $params = [];
        $websiteId = (int) $this->request->getParam('website', 0);
        $storeId = (int) $this->request->getParam('store', 0);

        if ($websiteId) {
            $params['website'] = $websiteId;
        }

        if ($storeId) {
            $params['store'] = $storeId;
        }

        $url = $this->helperBackend->getUrl('adminhtml/system_config/edit/section/web', $params);

        return __('To Enable, <a href="%1" target="_blank">Click here</a>, '
            . 'then open "Default Cookie Settings" section and set "Yes" next to the "Cookie Restriction Mode". Then save changes.', $url);
    }

    /**
     * Render inheritance checkbox (Use Default or Use Website)
     * Argument $element must be specified
     * We need to extend parent method
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderInheritCheckbox(// @codingStandardsIgnoreLine see docs
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        return '';
    }
}
