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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vpayments;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;

    protected $urlModel;
    public $_acl;
    public $_localeCurrency;

    protected $_session;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        UrlFactory $urlFactory,
        \Magento\Framework\Locale\Currency $localeCurrency,
        \Ced\CsMarketplace\Helper\Acl $acl
    )
    {
        $this->_session = $customerSession;
        $this->urlModel = $urlFactory;
        $this->_objectManager = $objectManager;
        $this->_acl = $acl;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $customerSession, $objectManager, $urlFactory);
    }

    /**
     * @return mixed
     */

    public function getVpayment()
    {
        $payment = $this->_objectManager->get('Magento\Framework\Registry')->registry('current_vpayment');
        return $payment;
    }

    /**
     * @return Ced_CsMarketplace_Block_Adminhtml_Vpayments_Details_Form
     */
    protected function _prepareForm()
    {
        list($model, $fieldsets) = $this->loadFields();
        $form = $this->_objectManager->get('Magento\Framework\Data\Form');


        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
            foreach ($data['fields'] as $id => $info) {
                if ($info['type'] == 'link') {
                    $fieldset->addField($id, $info['type'], [
                        'name' => $id,
                        'label' => $info['label'],
                        'title' => $info['label'],
                        'href' => $info['href'],
                        'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                        'text' => isset($info['text']) ? $info['text'] : $model->getData($id),
                        'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',
                    ]);
                } else {
                    $fieldset->addField($id, $info['type'], [
                        'name' => $id,
                        'label' => $info['label'],
                        'title' => $info['label'],
                        'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                        'text' => isset($info['text']) ? $info['text'] : $model->getData($id),
                        'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',

                    ]);
                }
            }
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadFields()
    {
        $model = $this->getVpayment();
        $renderOrderDesc = $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer\Orderdesc');

        $renderName = $this->_objectManager->create('Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname');
        if ($model->getBaseCurrency() != $model->getCurrency()) {
            $fieldsets = array(
                'beneficiary_details' => array(
                    'fields' => array(
                        'vendor_id' => array('label' => __('Vendor Name'), 'text' => $renderName->render($model), 'type' => 'note'),
                        'payment_code' => array('label' => __('Payment Method'), 'type' => 'label', 'value' => $model->getData('payment_code')),
                        'payment_detail' => array('label' => __('Beneficiary Details'), 'type' => 'note', 'text' => $model->getData('payment_detail')),
                    ),
                    'legend' => __('Beneficiary Details')
                ),

                'order_details' => array(
                    'fields' => array(
                        'amount_desc' => array(
                            'label' => __('Order Details'),
                            'text' => $renderOrderDesc->render($model),
                            'type' => 'note',
                        ),
                    ),
                    'legend' => __('Order Details')
                ),

                'payment_details' => array(
                    'fields' => array(
                        'transaction_id' => array('label' => __('Transaction ID#'), 'type' => 'label', 'value' => $model->getData('transaction_id')),
                        'created_at' => array(
                            'label' => __('Transaction Date'),
                            'value' => $model->getData('created_at'),
                            'type' => 'label',
                        ),
                        'payment_method' => array(
                            'label' => __('Transaction Mode'),
                            'value' => $this->_acl->getDefaultPaymentTypeLabel($model->getData('payment_method')),
                            'type' => 'label',
                        ),
                        'transaction_type' => array(
                            'label' => __('Transaction Type'),
                            'value' => ($model->getData('transaction_type') == 0) ? __('Credit Type') : __('Debit Type'),
                            'type' => 'label',
                        ),
                        'total_shipping_amount' => array(
                            'label' => __('Total Shipping Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('total_shipping_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'amount' => array(
                            'label' => __('Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'base_amount' => array(
                            'label' => '&nbsp;',
                            'value' => '[' . $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('base_amount'), false, 2, null, $model->getCurrency()) . ']',
                            'type' => 'label',
                        ),
                        'fee' => array(
                            'label' => __('Adjustment Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('fee'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'base_fee' => array(
                            'label' => '&nbsp;',
                            'value' => '[' . $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('base_fee'), false, 2, null, $model->getCurrency()) . ']',
                            'type' => 'label',
                        ),
                        'net_amount' => array(
                            'label' => __('Net Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('net_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'base_net_amount' => array(
                            'label' => '&nbsp;',
                            'value' => '[' . $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('base_net_amount'), false, 2, null, $model->getCurrency()) . ']',
                            'type' => 'label',
                        ),
                        'notes' => array(
                            'label' => __('Notes'),
                            'value' => $model->getData('notes'),
                            'type' => 'label',
                        ),
                    ),
                    'legend' => __('Transaction Details')
                ),
            );
        } else {
            $fieldsets = array(
                'beneficiary_details' => array(
                    'fields' => array(
                        'vendor_id' => array('label' => __('Vendor Name'), 'text' => $renderName->render($model), 'type' => 'note'),
                        'payment_code' => array('label' => __('Payment Method'), 'type' => 'label', 'value' => $model->getData('payment_code')),
                        'payment_detail' => array('label' => __('Beneficiary Details'), 'type' => 'note', 'text' => $model->getData('payment_detail')),
                    ),
                    'legend' => __('Beneficiary Details')
                ),

                'order_details' => array(
                    'fields' => array(
                        'amount_desc' => array(
                            'label' => __('Order Details'),
                            'text' => $renderOrderDesc->render($model),
                            'type' => 'note',
                        ),
                    ),
                    'legend' => __('Order Details')
                ),

                'payment_details' => array(
                    'fields' => array(
                        'transaction_id' => array('label' => __('Transaction ID#'), 'type' => 'label', 'value' => $model->getData('transaction_id')),
                        'created_at' => array(
                            'label' => __('Transaction Date'),
                            'value' => $model->getData('created_at'),
                            'type' => 'label',
                        ),
                        'payment_method' => array(
                            'label' => __('Transaction Mode'),
                            'value' => $this->_acl->getDefaultPaymentTypeLabel($model->getData('payment_method')),
                            'type' => 'label',
                        ),
                        'transaction_type' => array(
                            'label' => __('Transaction Type'),
                            'value' => ($model->getData('transaction_type') == 0) ? __('Credit Type') : __('Debit Type'),
                            'type' => 'label',
                        ),
                        'total_shipping_amount' => array(
                            'label' => __('Total Shipping Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('total_shipping_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'amount' => array(
                            'label' => __('Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'fee' => array(
                            'label' => __('Adjustment Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('fee'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'net_amount' => array(
                            'label' => __('Net Amount'),
                            'value' => $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface')->format($model->getData('net_amount'), false, 2, null, $model->getCurrency()),
                            'type' => 'label',
                        ),
                        'notes' => array(
                            'label' => __('Notes'),
                            'value' => $model->getData('notes'),
                            'type' => 'label',
                        ),
                    ),
                    'legend' => __('Transaction Details')
                ),
            );
        }

        return array($model, $fieldsets);
    }


    /**
     * Preparing global layout You can redefine this method in child classes for changin layout
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
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Vpayments\View\Element')
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
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
     * @return Varien_Data_Form
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
     * Set form object
     *
     * @param Varien_Data_Form $form
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function setForm(\Magento\Framework\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->getUrl());
        return $this;
    }


    /**
     * This method is called before rendering HTML
     * @return $this
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
     * @return $this
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = [])
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
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
                    $element->setFormat(
                        $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\Timezone')->getDateTimeFormat(\Magento\Framework\Stdlib\DateTime\Timezone::FORMAT_TYPE_SHORT)
                    );
                } else if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }

    /**
     * Add new element type
     *
     * @param Varien_Data_Form_Abstract $baseElement
     */
    protected function _addElementTypes(Varien_Data_Form_Abstract $baseElement)
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
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }

    /**
     * back Link url
     *
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array('_secure' => true, '_nosid' => true));
    }


}
