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

namespace Ced\CsMarketplace\Model\System\Config\Backend\Vproducts;
 
// Mage_Core_Model_Config_Data
class Category extends \Magento\Framework\App\Config\Value
{
    public function save()
    {
        if($value=$this->getValue()) {
            $value=implode(',', array_unique(explode(',', $this->getValue()))); 
        }
        $this->setValue($value);
        return parent::save();
    }
}
