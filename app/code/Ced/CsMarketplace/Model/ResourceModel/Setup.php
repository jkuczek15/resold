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
 
namespace Ced\CsMarketplace\Model\ResourceModel;
 
class Setup extends \Ced\CsMarketplace\Model\ResourceModel\Setup\AbstractModel
{

    protected $_objectManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }
    
    /**
     * Prepare vendor attribute values to save in additional table
     *
     * @param  array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge(
            $data, array(
            'is_visible'                => $this->_getValue($attr, 'visible', 1),
            'is_system'                 => $this->_getValue($attr, 'system', 1),
            'input_filter'              => $this->_getValue($attr, 'input_filter', null),
            'multiline_count'           => $this->_getValue($attr, 'multiline_count', 0),
            'validate_rules'            => $this->_getValue($attr, 'validate_rules', null),
            'data_model'                => $this->_getValue($attr, 'data', null),
            'sort_order'                => $this->_getValue($attr, 'position', 0)
            )
        );

        return $data;
    }
    
    /**
     * Add vendors attributes to customer forms
     *
     * @return void
     */
    public function installVendorForms()
    {
        $allowedAttributes = array(
                                'public_name',
                                'shop_url',
                                'created_at',
                                'status',
                                'group',
                                'name',
                                'gender',
                                'profile_picture',
                                'email',
                                'contact_number',
                                'company_name',
                                'about',
                                'company_logo',
                                'company_banner',
                                'company_address',
                                'support_number',
                                'support_email',
        );
        $typeId = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getEntityTypeId();

        $vendorAttributes = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute')->getCollection()
            ->addFieldToFilter('entity_type_id', array('eq'=>$typeId))
                                //->addFieldToFilter('attribute_code',array('in'=>$allowedAttributes))
            ->setOrder('attribute_id', 'ASC');

        foreach($vendorAttributes as $attribute) {
            $sortOrder = array_keys($allowedAttributes, $attribute->getAttributeCode());
            $sortOrder = isset($sortOrder[0])?$sortOrder[0]:0;
            $visibility = in_array($attribute->getAttributeCode(), $allowedAttributes)?1:0;
            $data[] = array(
            'attribute_id' => $attribute->getId(),
            'attribute_code' => $attribute->getAttributeCode(),
            'is_visible'   => $visibility,
            'sort_order'   => $sortOrder,
            'store_id'       => 0
            );
        }
        /* print_r($data);die; */
        if ($data) {
            $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')->insertMultiple($data);
        }
    }
    
    public function updateVendorAttributes() 
    {
        $vendorAttributes = array(
                                'shop_url'=>array('class'=>'validate-shopurl'),
                                'public_name' => array('class'=>'validate-no-html-tags'),
                                'created_at' => array('class'=>'validate-no-html-tags'),
                                'status' => array('class'=>'validate-no-html-tags'),
                                'group' => array('class'=>'validate-no-html-tags'),
                                'name' => array('class'=>'validate-no-html-tags'),
                                'gender' => array('class'=>'validate-no-html-tags'),
                                'profile_picture' => array('class'=>'validate-no-html-tags'),
                                'email' => array('class'=>'validate-email'),
                                'contact_number' =>  array('class'=>'validate-digits'),
                                'company_name' => array('class'=>'validate-no-html-tags'),
                                'about' => '',
                                'company_logo' => '',
                                'company_banner' => array('class'=>'validate-no-html-tags'),
                                'company_address' => array('class'=>'validate-no-html-tags'),
                                'support_number' => array('class'=>'validate-digits'),
                                'support_email' => array('class'=>'validate-email'),
                             );    
        foreach($vendorAttributes as $code=>$values) {
            $attributeModel = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute')->loadByCode('csmarketplace_vendor', $code);
            if (isset($values['class'])) {        
                $this->updateAttribute('csmarketplace_vendor', $attributeModel->getId(), 'frontend_class', $values['class']);
            }
        }
    }
    /*
    public function addAttribute($entityTypeId, $code, array $attr) 
    {
        parent::addAttribute($entityTypeId, $code, $attr);
        $attributeModel = $this->_objectManager->get('Magento\Eav\Model\Entity\Attribute')->loadByCode($entityTypeId, $code);        
        $is_visible = isset($attr['visible'])?(int)$attr['visible']:0;
        $position = isset($attr['position'])?(int)$attr['position']:0;
        $this->updateVendorForms($attributeModel, $is_visible, $position);
    }
    */
    
    public function updateVendorForms($attribute, $is_visible = 0, $position = 0) 
    {
        $joinFields = $this->_vendorForm($attribute);
        if(count($joinFields) > 0) {
            foreach($joinFields as $joinField) {
                $joinField->setData('is_visible', $is_visible);
                $joinField->setData('sort_order', $position);
                $joinField->save();
            }
        }
    }
    
    public function _vendorForm($attribute) 
    {
        $store = 0;        
        $fields = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')->getCollection()
            ->addFieldToFilter('attribute_id', array('eq'=>$attribute->getAttributeId()))
            ->addFieldToFilter('attribute_code', array('eq'=>$attribute->getAttributeCode()))
            ->addFieldToFilter('store_id', array('eq'=>$store));
        if(count($fields) == 0) {
            $data[] = array(
            'attribute_id' => $attribute->getId(),
            'attribute_code' => $attribute->getAttributeCode(),
            'is_visible'   => 0,
            'sort_order'   => 0,
            'store_id'       => $store
            );
            $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')->insertMultiple($data);
            return $this->_vendorForm($attribute);
        }
        return $fields;
    }

}
