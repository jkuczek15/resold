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


// phpcs:ignoreFile
if (php_sapi_name() == "cli") {
    return;
}

$configFile = BP . '/app/etc/typeahead.json';

if (!file_exists($configFile)) {
    echo \Zend_Json::encode([]);
    exit;
}

$config = \Zend_Json::decode(file_get_contents($configFile));

class TypeAheadAutocomplete
{
    private $config;

    public function __construct(
        array $config
    ) {
        $this->config = $config;
    }

    public function process()
    {
        $query = $this->getQueryText();
        $query = substr($query, 0, 2);
        return isset($this->config[$query])?$this->config[$query]:'';
    }

    private function getQueryText()
    {
        return isset($_GET['q']) ? $_GET['q'] : '';
    }
}

$result = (new \TypeAheadAutocomplete($config))->process();

echo \Zend_Json::encode($result);
exit;
