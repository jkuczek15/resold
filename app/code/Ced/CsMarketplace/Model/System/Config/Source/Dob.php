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
 * @package     Ced_CsMarketplace
 * @author 		   CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Model\System\Config\Source;
 
class Dob extends AbstractBlock
{

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
       /*   return [
            ['value' => 1, 'label'=>__('Male')],
            ['value' => 2, 'label'=>__('Female')],
            ['value' => 3, 'label'=>__('Common')]
        ]; */
         return ['1' => __('Male'),'2' => __('Female'),'3' => __('Other')];
       
    }

}
