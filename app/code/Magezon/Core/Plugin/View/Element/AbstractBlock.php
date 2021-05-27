<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Plugin\View\Element;

class AbstractBlock
{
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    public function aroundEscapeHtml(
    	$subject,
    	callable $proceed,
    	$data,
    	$allowedTags = null
    ) {

        $handles = $this->layout->getUpdate()->getHandles();
    	if (in_array('adminhtml_system_config_edit', $handles) && !is_array($data) && strpos($data, '<span class="mgz-logo">Magezon Extensions</span>') !== false) {
    		$result = $data;
    	} else {
    		$result = $proceed($data, $allowedTags);	
    	}
        return $result;
    }
}