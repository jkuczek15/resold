<?php
require __DIR__ . '/../app/bootstrap.php';

if(count($argv) < 4)
{
  echo "Usage: import_products.php <switch> <query> <vendor_id> [price] [pages].\r\n";
  echo "Switch is either -s for search query or -p for product ID.\r\n";
  exit;
}// end if not enough arguments provided

$_SERVER['switch'] = $argv[1];
$_SERVER['value'] = $argv[2];
$_SERVER['vendor_id'] = $argv[3];
if(count($argv) == 5)
{
  $_SERVER['price'] = $argv[4];
}// end if fourth argument provided
if(count($argv) == 6)
{
  $_SERVER['pages'] = $argv[5];
}// end if fourth argument provided

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('ImportProducts');
$bootstrap->run($app);
