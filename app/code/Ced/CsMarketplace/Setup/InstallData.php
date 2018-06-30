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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * CsMarketplace setup factory
     *
     * @var CsMarketplaceSetupFactory
     */
    private $csmarketplaceSetupFactory;

    public $_objectManager;

    /**
     * InstallData constructor.
     * @param CsMarketplaceSetupFactory $csmarketplaceSetupFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(CsMarketplaceSetupFactory $csmarketplaceSetupFactory, \Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->csmarketplaceSetupFactory = $csmarketplaceSetupFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        /**
         *
         *
         * @var CsMarketplaceSetup $csmarketplaceSetup
         */
        $csmarketplaceSetup = $this->csmarketplaceSetupFactory->create(['setup' => $setup]);

        $setup->startSetup();

        $csmarketplaceSetup->installEntities();
        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'created_at', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 1,
                'required' => false,
                'type' => 'datetime',
                'input' => 'label',
                'label' => 'Created At',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'shop_url', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 2,
                'type' => 'varchar',
                'label' => 'Shop Url',
                'input' => 'text',
                'required' => true,
                'frontend_class' => 'validate-shopurl',
                'validate_rules' => array(
                    'input_validation' => 'identifier'
                ),
                'user_defined' => false,
                'frontend_class' => 'validate-shopurl'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'status', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 3,
                'type' => 'varchar',
                'label' => 'Status',
                'input' => 'select',
                'source' => 'Ced\CsMarketplace\Model\System\Config\Source\Status',
                'default_value' => 'disabled',
                'required' => true,
                'user_defined' => false,
                'note' => '',
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'group', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 4,
                'type' => 'varchar',
                'label' => 'Vendor Group',
                'input' => 'select',
                'source' => 'Ced\CsMarketplace\Model\System\Config\Source\Group',
                'default_value' => 'general',
                'required' => true,
                'user_defined' => false,
                'note' => '',
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'public_name', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 4,
                'type' => 'varchar',
                'label' => 'Public Name',
                'input' => 'text',
                'required' => true,
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor',
            'website_id',
            array(
                'group' => 'General Information',
                'label' => 'Website ID',
                'type' => 'static',
                'user_defined' => false,
                'required' => false,

            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'name', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 5,
                'type' => 'varchar',
                'label' => 'Name',
                'input' => 'text',
                'required' => true,
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'gender', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 6,
                'required' => false,
                'type' => 'int',
                'input' => 'select',
                'label' => 'Gender',
                'source' => 'Ced\CsMarketplace\Model\System\Config\Source\Dob',
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'profile_picture', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 7,
                'required' => false,
                'type' => 'varchar',
                'input' => 'image',
                'label' => 'Profile Picture',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'email', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 8,
                'required' => true,
                'unique' => true,
                'type' => 'varchar',
                'input' => 'text',
                'source' => '',
                'label' => 'Email',
                'frontend_class' => 'validate-email',
                'validate_rules' => array(
                    'input_validation' => 'email'
                ),
                'user_defined' => false,
                'frontend_class' => 'validate-email'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'contact_number', array(
                'group' => 'General Information',
                'visible' => true,
                'position' => 9,
                'required' => false,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Contact Number',
                'frontend_class' => 'validate-digits',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-digits'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'company_name', array(
                'group' => 'Company Information',
                'visible' => true,
                'position' => 10,
                'required' => false,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Company Name',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'about', array(
                'group' => 'Company Information',
                'visible' => true,
                'position' => 11,
                'required' => false,
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'About',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'company_logo', array(
                'group' => 'Company Information',
                'required' => false,
                'visible' => true,
                'position' => 12,
                'type' => 'varchar',
                'input' => 'image',
                'label' => 'Company Logo',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'company_banner', array(
                'group' => 'Company Information',
                'visible' => true,
                'position' => 13,
                'required' => false,
                'type' => 'varchar',
                'input' => 'image',
                'label' => 'Company Banner',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'company_address', array(
                'group' => 'Company Information',
                'visible' => true,
                'position' => 14,
                'required' => false,
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Company Address',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-no-html-tags'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'support_number', array(
                'group' => 'Support Information',
                'visible' => true,
                'position' => 15,
                'required' => false,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Support Number',
                'frontend_class' => 'validate-digits',
                'source' => '',
                'user_defined' => false,
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'support_email', array(
                'group' => 'Support Information',
                'visible' => true,
                'position' => 16,
                'required' => false,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Support Email',
                'frontend_class' => 'validate-email',
                'source' => '',
                'user_defined' => false,
                'frontend_class' => 'validate-email'
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'meta_keywords', array(
                'group' => 'SEO Information',
                'visible' => true,
                'position' => 19,
                'required' => false,
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Meta Keywords',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'meta_description', array(
                'group' => 'SEO Information',
                'visible' => true,
                'position' => 20,
                'required' => false,
                'type' => 'text',
                'input' => 'textarea',
                'label' => 'Meta Description',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'facebook_id', array(
                'group' => 'Support Information',
                'visible' => true,
                'position' => 21,
                'required' => false,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Facebook ID',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'twitter_id', array(
                'group' => 'Support Information',
                'visible' => true,
                'position' => 22,
                'required' => false,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Twitter ID',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->removeAttribute('csmarketplace_vendor', 'address');
        $csmarketplaceSetup->removeAttribute('csmarketplace_vendor', 'city');
        $csmarketplaceSetup->removeAttribute('csmarketplace_vendor', 'zip_code');
        $csmarketplaceSetup->removeAttribute('csmarketplace_vendor', 'region_id');
        $csmarketplaceSetup->removeAttribute('csmarketplace_vendor', 'country_id');
        $csmarketplaceSetup->removeAttribute('csmarketplace_vendor', 'region');

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'address', array(
                'group' => 'Address Information',
                'visible' => true,
                'position' => 25,
                'required' => true,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'Address',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'city', array(
                'group' => 'Address Information',
                'visible' => true,
                'position' => 26,
                'required' => true,
                'type' => 'varchar',
                'input' => 'text',
                'label' => 'City',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'zip_code', array(
                'group' => 'Address Information',
                'visible' => true,
                'position' => 27,
                'required' => true,
                'type' => 'int',
                'input' => 'text',
                'label' => 'Zip/Postal Code',
                'source' => '',
                'user_defined' => false
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'region', array(
                'group' => 'Address Information',
                'visible' => true,
                'position' => 29,
                'type' => 'varchar',
                'label' => 'State',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'user_defined' => false,
                'note' => ''
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'region_id', array(
                'group' => 'Address Information',
                'visible' => true,
                'position' => 28,
                'type' => 'int',
                'label' => 'State',
                'input' => 'select',
                'source' => 'Ced\CsMarketplace\Model\Vendor\Address\Source\Region',
                'required' => false,
                'user_defined' => false,
                'note' => ''
            )
        );

        $csmarketplaceSetup->addAttribute(
            'csmarketplace_vendor', 'country_id', array(
                'group' => 'Address Information',
                'visible' => true,
                'position' => 30,
                'type' => 'varchar',
                'label' => 'Country',
                'input' => 'select',
                'source' => 'Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country',
                'required' => true,
                'user_defined' => false,
                'note' => ''
            )
        );

        $registrationFormAttributes = array('public_name' => 10, 'shop_url' => 20);
        $profileDisplayAttributes = array(
            'public_name' => array('sort_order' => 10, 'fontawesome' => 'fa fa-user', 'store_label' => 'Public Name'),
            'support_number' => array('sort_order' => 20, 'fontawesome' => 'fa fa-mobile', 'store_label' => 'Tel'),
            'support_email' => array('sort_order' => 30, 'fontawesome' => 'fa fa-envelope-o', 'store_label' => 'Support Email'),
            'email' => array('sort_order' => 35, 'fontawesome' => 'fa fa-envelope-o', 'store_label' => 'Email'),
            'company_name' => array('sort_order' => 40, 'fontawesome' => 'fa fa-building', 'store_label' => 'Company'),
            'name' => array('sort_order' => 50, 'fontawesome' => 'fa fa-user', 'store_label' => 'Representative'),
            'company_address' => array('sort_order' => 60, 'fontawesome' => 'fa fa-location-arrow', 'store_label' => 'Location'),
            'created_at' => array('sort_order' => 70, 'fontawesome' => 'fa fa-calendar', 'store_label' => 'Vendor Since'),
            'facebook_id' => array('sort_order' => 80, 'fontawesome' => 'fa fa-facebook-square', 'store_label' => 'Find us on Facebook'),
            'twitter_id' => array('sort_order' => 90, 'fontawesome' => 'fa fa-twitter', 'store_label' => 'Follow us on Twitter')
        );
        $vendorAttributes = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')->getCollection();
        if (count($vendorAttributes) > 0) {
            $storesViews = $this->_objectManager->get('Magento\Store\Model\Store')->getCollection();
            foreach ($vendorAttributes as $vendorAttribute) {
                $vendorMainAttribute = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Form')
                                        ->load($vendorAttribute->getAttributeId());
                $isSaveNeeded = false;
                if (isset($registrationFormAttributes[$vendorAttribute->getAttributeCode()])) {
                    $vendorAttribute->setData('use_in_registration', 1);
                    $vendorAttribute->setData('position_in_registration', $registrationFormAttributes[$vendorAttribute->getAttributeCode()]);
                    $isSaveNeeded = true;
                }
                if (isset($profileDisplayAttributes[$vendorAttribute->getAttributeCode()])) {
                    $frontend_label[0] = $vendorMainAttribute->getFrontendLabel();
                    foreach ($storesViews as $storesView) {
                        $frontend_label[$storesView->getId()] = $profileDisplayAttributes[$vendorAttribute->getAttributeCode()]['store_label'];
                    }
                    $vendorAttribute->setData('use_in_left_profile', 1);
                    $vendorAttribute->setData('position_in_left_profile', $profileDisplayAttributes[$vendorAttribute->getAttributeCode()]['sort_order']);
                    $vendorAttribute->setData('fontawesome_class_for_left_profile', $profileDisplayAttributes[$vendorAttribute->getAttributeCode()]['fontawesome']);
                    $vendorMainAttribute->setData('frontend_label', $frontend_label);
                    $isSaveNeeded = true;
                }
                if ($isSaveNeeded) {
                    $vendorAttribute->save();
                    $vendorMainAttribute->save();
                    $isSaveNeeded = false;
                }
            }
        }
        $setup->endSetup();
    }
}
