<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Custom\Sell\Model;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        return [];
    }
}
