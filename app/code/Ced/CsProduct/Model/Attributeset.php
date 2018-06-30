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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

/**
 * Vendor Product model
 *
 * @category   Ced
 * @package    Ced_CsProduct
 * @author     CedCommerce Core Team <connect@cedcommerce.com>
 */

namespace Ced\CsProduct\Model;

class Attributeset extends \Magento\Framework\Model\AbstractModel {

  /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
  */
  protected $_scopeConfig;

  /**
   * @var \Magento\Framework\ObjectManagerInterface
  */
  protected $_objectManager;

  /**
   * @var \Magento\Store\Model\StoreManagerInterface
  */
  protected $_storeManager;

   public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $_storeManager;
        parent::__construct($context, $registry);
    }

  public function getAllowedAttributeSets()
  {
        if($this->_scopeConfig == null)
            $this->_scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        
        $allowedSet = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Vproducts\Set')->getAllowedSet($this->_storeManager->getStore()->getId());
        $allowed_attr_sets = array();
        foreach ($allowedSet as $setId) {
            $set = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')->load($setId);
            if($set && $set->getId()) {
	      $set->getData();
              $allowed_attr_sets[] = array('value'=> $set['attribute_set_id'], 'label'=> $set['attribute_set_name']);
	    }	
        }
        return $allowed_attr_sets;
  }
}

