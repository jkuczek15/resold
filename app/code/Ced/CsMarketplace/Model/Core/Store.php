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
 namespace Ced\CsMarketplace\Model\Core;

class Store extends \Magento\Store\Model\Store
{

    protected $_objectManager;
  
    public function getConfig($path)
    {
        $path = $this->preparePath($path);
        $data = $this->_config->getValue($path, 'store', $this->getCode());
        if (!$data) {
            $data = $this->_config->getValue($path,  'default');
        }
        return $data === false ? null : $data;
    }

    public function preparePath($path, $group = null,$case = 1) 
    {
        if(!preg_match('/ced_/i', $path) || preg_match('/'.preg_quote('ced_csgroup/general/activation', '/').'/i', $path)) { return $path;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->_objectManager = $objectManager;
        
        if($group == null) {
            switch($case) {
            case 1: 
                if($this->_objectManager->create('Magento\Framework\Module\Manager')->isEnabled('Ced_CsCommission')) {
                    if($this->_objectManager->create('Magento\Framework\Registry')->registry('ven_id')) {
                        $vendor = $this->_objectManager->create('Magento\Framework\Registry')->registry('ven_id');
                        if (is_numeric($this->_objectManager->create('Magento\Framework\Registry')->registry('ven_id'))) {
                            $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($this->_objectManager->get('Magento\Framework\Registry')->registry('ven_id'));
                        }
                        if ($vendor && is_object($vendor) && $vendor->getId()) {
                            return $vendor->getId().'/'.$path;
                        }
                    }
                }
                return $path;
              break;
            default: 
                return $path;
              break;
            }
        } else {
            return $path;
        }
    }
}
