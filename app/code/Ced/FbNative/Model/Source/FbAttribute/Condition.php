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

class Condition extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * Options getter
     *
     * @return array
     */
    public function getAllOptions()
    {
        return [
            [
                'label' => '',
                'value' => ''
            ],
            [
                'label' => 'New',
                'value' =>'new'
            ],
            [
                'label' => 'Refurbished',
                'value' =>'refurbished'
            ],
            [
                'label' => 'Used',
                'value' =>'used'
            ],
            [
                'label' => 'CPO',
                'value' =>'cpo'
            ]
        ];

    }

}
