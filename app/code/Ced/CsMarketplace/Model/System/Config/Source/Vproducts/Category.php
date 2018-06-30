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
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model\System\Config\Source\Vproducts;
 
class Category extends \Ced\CsMarketplace\Model\System\Config\Source\AbstractBlock
{
    /**
     * Supported Product Type by Ced_CsMarketplace extension.
     */     
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_CATEGORY_MODE = 'global/ced_csmarketplace/vproducts/category_mode';
    const XML_PATH_CED_CSMARKETPLACE_VPRODUCTS_CATEGORY = 'global/ced_csmarketplace/vproducts/category';
    
    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {  
        $options = array();
        $options[] = array('value' => '0', 'label'=>__('All Allowed Categories'));
        $options[] = array('value' => '1', 'label'=>__('Specific Categories'));
        return $options;        
    }

}
