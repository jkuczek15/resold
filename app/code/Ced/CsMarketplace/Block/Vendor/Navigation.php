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

namespace Ced\CsMarketplace\Block\Vendor;

class Navigation extends AbstractBlock
{
    protected $_activeLink = false;
  
    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }
    
    public function isActive($link)
    {
        if (empty($this->_activeLink)) {
            $this->_activeLink = $this->getAction()->getFullActionName('/');
        }
        if ($this->_completePath($link->getPath()) == $this->_activeLink) {
            return true;
        } else {
			if(count($link->getChildren()) > 0) {
				$isParentActive = false;
				foreach($link->getChildren() as $ch1_link) {
					if($this->isActive($ch1_link)){
						$isParentActive = true;
						break;
					}
				}
				return $isParentActive;
			}
		}
        return false;
    }

    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
            case 2:
                $path .= '/index';
        }
        return $path;
    }
		
	public function isPaymentDetailAvailable () {
		return count($this->getVendor()->getPaymentMethodsArray($this->getVendorId(),false));
	}
}