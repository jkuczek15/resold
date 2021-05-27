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

namespace Ced\CsMarketplace\Controller\Vsettings;

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    /* protected $resultPageFactory;
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    } */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) { return; 
        }
        $section = $this->getRequest()->getParam('section', '');
        $groups = $this->getRequest()->getPost('groups', array());
        if(strlen($section) > 0 && $this->_getSession()->getData('vendor_id') && count($groups)>0) {
            $vendor_id = (int)$this->_getSession()->getData('vendor_id');
            try {
                foreach ($groups as $code => $values) {
                    foreach ($values as $name => $value) {
                        $serialized = 0;
                        $key = strtolower($section.'/'.$code.'/'.$name);
                        if (is_array($value)) {  
                            $value = serialize($value); 
                            $serialized = 1; 
                        }
                        $setting=false;
                        $setting = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings')->loadByField(array('key','vendor_id'), array($key,$vendor_id));

                        if ($setting && $setting->getId()) {
                            $setting->setVendorId($vendor_id)
                                ->setGroup($section)
                                ->setKey($key)
                                ->setValue($value)
                                ->setSerialized($serialized)
                                ->save();
                        } else {
                            
                            $setting = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings');
                            $setting->setVendorId($vendor_id)
                                ->setGroup($section)
                                ->setKey($key)
                                ->setValue($value)
                                ->setSerialized($serialized)
                                ->save();
                        }
                    }
                }
            
                //	$this->_getSession()->addSuccessMessage(__('The setting information has been saved.'));
                $this->_redirect('*/*');
                return;
            } catch (\Exception $e) {
                //	$this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*');
                return;
            }
        }
        $this->_redirect('*/*');
    }
}

