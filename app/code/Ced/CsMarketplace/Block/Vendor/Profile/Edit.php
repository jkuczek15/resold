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

namespace Ced\CsMarketplace\Block\Vendor\Profile;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;

class Edit extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

    public $_objectManager;
    public $_formFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        UrlFactory $urlFactory,
        \Magento\Framework\Data\FormFactory $formFactory
    )
    {
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
        $this->_formFactory = $formFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get collection of Vendor Attributes
     */
    public function getVendorAttributes()
    {
        $vendorAttributes = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Attribute')
            ->setStoreId($this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId())
            ->getCollection()
            ->addFieldToFilter('is_visible', array('gt' => 0))
            ->setOrder('sort_order', 'ASC');

        $this->_eventManager->dispatch(
            'ced_csmarketplace_vendor_edit_attributes_load_after',
            ['vendorattributes' => $vendorAttributes]
        );

        $vendorAttributes->getSelect()->having('vform.is_visible >= 0');

        return $vendorAttributes;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Widget\Form\Renderer\Element')
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Widget\Form\Renderer\Fieldset')
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Widget\Form\Renderer\Fieldset\Element')
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form object
     *
     * @deprecated deprecated since version 1.2
     * @see getForm()
     */
    public function getFormObject()
    {
        return $this->getForm();
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @return $this
     */
    public function setForm(\Magento\Framework\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->getBaseUrl());
        return $this;
    }

    /**
     * Prepare form before rendering HTML
     */
    protected function _prepareForm()
    {

        $vendorformFields = $this->getVendorAttributes();

        $form = $this->_formFactory->create([
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setUseContainer(true);

        $model = $this->getVendorId() ? $this->getVendor()->getData() : [];

        $id = $this->getVendorId();
        foreach ($vendorformFields as $attribute) {
            $ascn = 0;

            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if ($inputType = $attribute->getFrontend()->getInputType()) {
                if (!isset($model[$attribute->getAttributeCode()]) || (isset($model[$attribute->getAttributeCode()]) && !$model[$attribute->getAttributeCode()])) {
                    $model[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
                if ($inputType == 'boolean') $inputType = 'select';
                if (in_array($attribute->getAttributeCode(), \Ced\CsMarketplace\Model\Form::$VENDOR_FORM_READONLY_ATTRIBUTES)) {
                    continue;
                }

                $fieldType = $inputType;


                $requiredText = "";
                if (strpos($attribute->getFrontend()->getClass(), 'required') !== false) {
                    $requiredText = "*";
                }

                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $form->addType($fieldType, $rendererClass);
                }

                $element = $form->addField($attribute->getAttributeCode(), $fieldType,
                    array(
                        'name' => "vendor[" . $attribute->getAttributeCode() . "]",
                        'label' => $attribute->getStoreLabel() ? $requiredText . ' ' . $attribute->getStoreLabel() : $requiredText . ' ' . __($attribute->getFrontend()->getLabel()),
                        'class' => $ascn && $attribute->getAttributeCode() == 'shop_url' && $id ? '' : $attribute->getFrontend()->getClass(),
                        'required' => $ascn && $attribute->getAttributeCode() == 'shop_url' && $id ? false : $attribute->getIsRequired(),
                        $ascn && in_array($attribute->getAttributeCode(), array('shop_url', 'email')) && $id ? 'href' : '' => $ascn && in_array($attribute->getAttributeCode(), array('shop_url', 'email')) && $id ? $shopUrl : '',
                        $ascn && in_array($attribute->getAttributeCode(), array('shop_url', 'email')) && $id ? 'target' : '' => $ascn && $id ? $attribute->getAttributeCode() == 'shop_url' ? '_blank' : '' : '',
                        'value' => $model[$attribute->getAttributeCode()],
                    )
                )
                    ->setEntityAttribute($attribute);

                if($element->getExtType() == 'file'){
                    if ($element->getValue()) {
                        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$element->getValue();
                        $element->setAfterElementHtml('<p><a href="'.$url.'" target="_blank" >'.$element->getLabel().' '.__("Download").'</a></p>');
                    }
                }else{
                    $element->setAfterElementHtml('');
                }

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setFormat($this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\Timezone')->getDateFormatWithLongYear());
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }

        $form->addField('password-check','checkbox',
            array(
                'label'     => "Change Password",
                'class'     => "password-checkbox",
                'name'      =>"change_password"
            )
        );

        $form->addField('current_password','password',
            array(
                'name'      => "vendor[current_password]",
                'label'     => "Current Password",
                'class'     => "change_password form-control input-text",
                'value'     => '',
            )
        );
        $form->addField('new_password','password',
            array(
                'name'      => "vendor[new_password]",
                'label'     => "New Password",
                'class'     => "change_password form-control input-text validate-password",
                'value'  => '',
            )
        );
        $form->addField('confirm_password','password',
            array(
                'name'      => "vendor[confirm_password]",
                'label'     => "Confirm Password",
                'class'     => "change_password form-control input-text validate-cpassword",
                'value'  => '',
            )
        );
        
        $this->setForm($form);
        return $this;
    }

    /**
     * @return region Id
     */
    public function getRegionId()
    {
        $region = 0;
        $model = $this->getVendorId() ? $this->getVendor()->getData() : [];
        if (isset($model['region_id'])) {
            $region = $model['region_id'];
        }
        return $region;
    }

    /**
     * This method is called before rendering HTML
     *
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        $this->_initFormValues();
        return parent::_beforeToHtml();
    }

    /**
     * Initialize form fields values
     * Method will be called after prepareForm and can be used for field values initialization
     */
    protected function _initFormValues()
    {
        return $this;
    }


    /**
     * Set Fieldset to Form
     * @param $attributes
     * @param $fieldset
     * @param array $exclude
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = [])
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType())
                && !in_array($attribute->getAttributeCode(), $exclude)
                && ('media_image' != $inputType)
            ) {

                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    array(
                        'name' => $attribute->getAttributeCode(),
                        'label' => $attribute->getFrontend()->getLabel(),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                        'note' => $attribute->getNote(),
                    )
                )
                    ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } else if ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } else if ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setFormat($this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\Timezone')->getDateFormatWithLongYear());
                } else if ($inputType == 'datetime') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setTime(true);
                    $element->setStyle('width:50%;');

                    $element->setFormat($this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\Timezone')->getDateFormatWithLongYear());

                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }


    /**
     * Add new element type
     * @param \Magento\Framework\Data\Form\AbstractForm $baseElement
     */
    protected function _addElementTypes(\Magento\Framework\Data\Form\AbstractForm $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }

    /**
     * Retrieve predefined additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [];
    }


    /**
     * get Additional Description
     * @param $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }

}
