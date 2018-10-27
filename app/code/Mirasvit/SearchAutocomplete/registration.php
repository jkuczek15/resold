<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.65
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


if (isset($_SERVER)
    && is_array($_SERVER)
    && isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], 'searchautocomplete/ajax/typeahead') !== false) {
    require_once 'typeahead.php';
}

$registration = dirname(dirname(dirname(__DIR__)))
    . '/vendor/mirasvit/module-search-autocomplete/src/SearchAutocomplete/registration.php';
if (file_exists($registration)) {
    # module was already installed via composer
    return;
}

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Mirasvit_SearchAutocomplete',
    __DIR__
);
