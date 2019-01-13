<?php
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_Walmart
  * @author      CedCommerce Core Team <connect@cedcommerce.com>
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\FbNative\Model\Source\FbAttribute;

class FbAttributes implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => '',
                'value' => ''
            ],
            [
                'label' => 'Name',
                'value' =>'name'
            ],
            [
                'label' => 'Title',
                'value' =>'title'
            ],
            [
                'label' => 'GTIN',
                'value' =>'gtin'
            ],
            [
                'label' => 'MPN',
                'value' =>'mpn'
            ],
            [
                'label' => 'Target Country',
                'value' =>'targetCountry'
            ],
            [
                'label' => 'Content Language',
                'value' =>'contentLanguage'
            ],
            [
                'label' => 'Condition',
                'value' =>'facebook_condition'
            ],
            [
                'label' => 'Google Product Category',
                'value' =>'google_product_category'
            ]
        ];

    }

}
