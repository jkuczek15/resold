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

namespace Ced\CsMarketplace\Model\System\Config\Source;

class Group extends AbstractBlock
{
     public static $GROUPS_ARRAY = array();
    
    const XML_PATH_CED_CSMARKETPLACE_VENDOR_GROUPS = 'ced_csmarketplace/vendor/groups';
    
    protected $_scopeConfig;
    
    /**
     * 
     *
     * @var \Magento\Framework\App\Helper\Context 
    */
    protected $_context;
    protected $_cedeventManager;
    /**
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
     
     
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
    	//\Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Helper\Context $context
    ) {

	parent::__construct($attrOptionCollectionFactory, $attrOptionFactory, $objectManager);
        $this->_context = $context;
        $this->_cedeventManager = $context->getEventManager();

    }
    /**
     * Retrieve groups data form config.xml
     *
     * @return array
     */
    public function getGroups() 
    {
        $groups = $this->_context->getScopeConfig()->getValue(self::XML_PATH_CED_CSMARKETPLACE_VENDOR_GROUPS);
        self::$GROUPS_ARRAY = json_decode(json_encode($groups), true);
       $this->_cedeventManager->dispatch(
        		'ced_csmarketplace_vendor_group_prepare',
        		[ 'class' =>'Ced\CsMarketplace\Model\System\Config\Source\Group']
        );
        return self::$GROUPS_ARRAY;
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    
    public function toOptionArray()
    {
          $options = [];
        if(is_array($this->getGroups())){
            $groups = array_keys($this->getGroups());
           
            foreach($groups as $group) {
                $group = strtolower(trim($group));
               	$options[]=array('value'=>$group,'label'=>__(ucfirst($group)));
            }
        }
        return $options;
    }
    public function getVendorGroups()
    {
    	$groups = array_keys($this->getGroups());
    	$options = array();
    	foreach($groups as $group) {
    		$group = strtolower(trim($group));
    			
    		$options[]=array('value'=>$group,'label'=>__(ucfirst($group)));
    	}
    
    	return $options;
    }

    public function toFilterOptionArray($defaultValues = false, $withEmpty = false,$storeId=null) {
		if($storeId==null)
			$options = $this->toOptionArray();
		else 
			$options = $this->toOptionArray();
		$filterOptions = [];
		if(count($options)) {
			foreach($options as $option) {
				if(isset($option['value']) && isset($option['label'])) {
					$filterOptions[$option['value']] = $option['label'];
				}
			}
		}
		return $filterOptions;
	}
}
