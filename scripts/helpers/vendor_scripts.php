<?php
/*
    file:       magento/framework/Config/Reader/Filesystem.php
    method:     protected function _readFiles($fileList)
    usage:      helps to debug errors in XML files
*/

foreach($configMerger->getDom()->getElementsByTagName('route') as $element ){

    if (!$element->hasAttribute('frontName')) {
        var_dump($configMerger->getDom()->saveXML($element));
    }
}