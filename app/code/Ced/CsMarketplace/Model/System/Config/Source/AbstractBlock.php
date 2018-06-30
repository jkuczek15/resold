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
 
class AbstractBlock extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{

    protected $_objectManager;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory            $attrOptionFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->_objectManager = $objectManager;
    }

    /**
     * Retrieve Full Option values array
     *
     * @param  bool $withEmpty     Add empty option to array
     * @param  bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $storeId = $this->getAttribute()->getStoreId();
        $this->_options[$storeId] = $this->_optionsDefault[$storeId] = $this->toOptionArray();
        if (!is_array($this->_options)) {
            $this->_options = [];
        }
        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = [];
        }
        if (!isset($this->_options[$storeId])) {
            $collection = $this->_attrOptionCollectionFactory->create()
                ->setPositionOrder('asc')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setStoreFilter($this->getAttribute()->getStoreId())
                ->load();
            $this->_options[$storeId]        = $collection->toOptionArray();
            $this->_optionsDefault[$storeId] = $collection->toOptionArray();
        }
        $options = ($defaultValues ? $this->_optionsDefault[$storeId] : $this->_options[$storeId]);
        if ($withEmpty) {
            array_unshift($options, array('label' => '', 'value' => ''));
        }
        return $options;
    }
    
    
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (strpos($value, ',') !==false) {
            $isMultiple = true;
            $value = explode(',', $value);
        }
        
        $options = $this->getAllOptions(false);
        
        if ($isMultiple) {
            $values = [];
            foreach ($options as $key => $item) {
                if (in_array($key, $value)) {
                    $values[] = $item;
                }
            }
            return $values;
        }
        foreach ($options as $key => $item) {
            if ($key == $value) {
                return $item;
            }
        }
        return false;
    }
    
    /**
     * Retrieve options for grid filter
     *
     * @param  String $value
     * @return String
     */
    public function toFilterOptionArray($defaultValues = false, $withEmpty = false,$storeId=null) 
    {
        if($storeId==null) {
            $options = $this->toOptionArray($defaultValues, $withEmpty); 
        }
        else { 
            $options = $this->toOptionArray($defaultValues, $withEmpty, $storeId); 
        }
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
    
    /**
     * Retrieve option label by option value
     *
     * @param  String $value
     * @return String
     */
    public function getLabelByValue($value = '') 
    {
        $options = $this->toOptionArray();
        if(count($options)) {
            foreach($options as $option) {
                if(isset($option['value']) && $option['value'] == $value) {
                    $value = isset($option['label'])?$option['label']:$value;
                    break;
                }
            }
        }
        return $value;
    }
    


}
