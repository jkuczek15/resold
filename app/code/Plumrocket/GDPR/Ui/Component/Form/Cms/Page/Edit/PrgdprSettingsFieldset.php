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

namespace Plumrocket\GDPR\Ui\Component\Form\Cms\Page\Edit;

/**
 * Class PrgdprSettingsFieldset
 */
class PrgdprSettingsFieldset extends \Magento\Ui\Component\Form\Fieldset
{
    /**
     * @var \Plumrocket\GDPR\Helper\Data
     */
    private $dataHelper;

    /**
     * PrgdprSettingsFieldset constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Plumrocket\GDPR\Helper\Data $dataHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Plumrocket\GDPR\Helper\Data $dataHelper,
        $components = [],
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $components, $data);
    }

    /**
     * Get component configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        $config = parent::getConfiguration();

        if (! $this->dataHelper->moduleEnabled()) {
            $config['visible'] = false;
        }

        return $config;
    }
}
