<?php
return array (
  'backend' =>
  array (
    'frontName' => 'stm',
  ),
  'crypt' =>
  array (
    'key' => '<your key>',
  ),
  'session' =>
  array (
    'save' => 'files'
  ),
  'db' =>
  array (
    'table_prefix' => '',
    'connection' =>
    array (
      'default' =>
      array (
        'host' => '<your db host>',
        'dbname' => 'MagentoQuickstartDB',
        'username' => '<your db user>',
        'password' => '<your db password>',
        'model' => 'mysql4',
        'engine' => 'innodb',
        'initStatements' => 'SET NAMES utf8;',
        'active' => '1',
      ),
    ),
  ),
  'resource' =>
  array (
    'default_setup' =>
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'production',
  'cache_types' =>
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'customer_notification' => 1,
    'full_page' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'config_webservice' => 1,
    'translate' => 1,
    'compiled_config' => 1,
  ),
  'install' =>
  array (
    'date' => 'Sun, 28 Oct 2018 17:43:33 +0000',
  ),
);
