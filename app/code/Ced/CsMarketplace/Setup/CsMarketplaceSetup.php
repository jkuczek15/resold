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

namespace Ced\CsMarketplace\Setup;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;

/**
 * @codeCoverageIgnore
 */
class CsMarketplaceSetup extends EavSetup
{
    /**
     * EAV configuration
     *
     * @var Config
     */
    protected $eavConfig;

    /**
     * Setup model
     *
     * @var ModuleDataSetupInterface
     */
    private $setup;
    
    protected $_vendorAttributeFactory;
    
    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context                  $context
     * @param CacheInterface           $cache
     * @param CollectionFactory        $attrGroupCollectionFactory
     * @param Config                   $eavConfig
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        Config $eavConfig,
        \Ced\CsMarketplace\Model\Vendor\FormFactory $vendorAttributeFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->setup = $setup;
        $this->_vendorAttributeFactory = $vendorAttributeFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Retrieve default entities: vendor, vendor_address
     *
     * @return                                        array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        $entities = [
            'csmarketplace_vendor' => [
                'entity_model' => 'Ced\CsMarketplace\Model\ResourceModel\Vendor',
                'table' => 'ced_csmarketplace_vendor',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'attributes' => [
                    'customer_id' => [
                        'group'         => 'General Information',
                        'visible'       => false,
                        'position'      => 0,
                        'type'          => 'int',
                        'label'         => 'Associated Customer',
                        'input'         => 'select',
                        'adminhtml_only'=> 1,
                        'source'        => 'Ced\CsMarketplace\Model\System\Config\Source\Customers',
                        'required'      => true,
                        'user_defined'  => false,
                        'unique'        => true,
                        'note'          => "After selecting customer association can't be changed."
                    ],
                ],
            ],
        ];
        return $entities;
    }

    /**
     * Gets EAV configuration
     *
     * @return Config
     */
    public function getEavConfig()
    {
        return $this->eavConfig;
    }
    
    /**
     * Add attribute to an entity type
     *
     * If attribute is system will add to all existing attribute sets
     *
     * @param  string|integer $entityTypeId
     * @param  string         $code
     * @param  array          $attr
     * @return $this
     */
    public function addAttribute($entityTypeId, $code, array $attr) 
    {
        parent::addAttribute($entityTypeId, $code, $attr);        
        $is_visible = isset($attr['visible'])?(int)$attr['visible']:0;
        $position = isset($attr['position'])?(int)$attr['position']:0;
        $this->updateVendorForms($this->getAttributeId($entityTypeId, $code), $code, $is_visible, $position);
        return $this;
    }
    
    
    public function updateVendorForms($attributeId, $code, $is_visible = 0, $position = 0) 
    {
        $joinFields = $this->_vendorForm($attributeId, $code);
        if(count($joinFields) > 0) {
            foreach($joinFields as $joinField) {
                $joinField->setData('is_visible', $is_visible);
                $joinField->setData('sort_order', $position);
                $joinField->save();
            }
        }
    }
    
    public function _vendorForm($attributeId, $code) 
    {
        $store = 0;        
        $fields =  $this->_vendorAttributeFactory->create()->getCollection()
                    ->addFieldToFilter('attribute_id', array('eq'=>$attributeId))
                    ->addFieldToFilter('attribute_code', array('eq'=>$code))
                    ->addFieldToFilter('store_id', array('eq'=>$store));
        if(count($fields) == 0) {
            $data[] = [
                'attribute_id' => $attributeId,
                'attribute_code' => $code,
                'is_visible' => 0,
                'sort_order' => 0,
                'store_id' => $store
            ];
            $this->setup->getConnection()->insertMultiple($this->setup->getTable('ced_csmarketplace_vendor_form_attribute'), $data);
            return $this->_vendorForm($attributeId, $code);
        }
        return $fields;
    }
}
