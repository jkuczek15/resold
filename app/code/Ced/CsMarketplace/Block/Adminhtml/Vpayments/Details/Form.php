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

namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Details;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_acl;
    protected $_coreRegistry;
    protected $_objectManager;
    protected $_localeCurrency;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Locale\Currency $localeCurrency
     * @param \Ced\CsMarketplace\Helper\Acl $acl
     * @param array $data
     */


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Locale\Currency $localeCurrency,
        \Ced\CsMarketplace\Helper\Acl $acl,
        array $data = []
    )
    {
        $this->_acl = $acl;
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        list($model, $fieldsets) = $this->loadFields();
        $form = $this->_formFactory->create();
        foreach ($fieldsets as $key => $data) {
            $fieldset = $form->addFieldset($key, array('legend' => $data['legend']));
            foreach ($data['fields'] as $id => $info) {
                if ($info['type'] == 'link') {
                    $fieldset->addField($id, $info['type'], array(
                        'name' => $id,
                        'label' => $info['label'],
                        'title' => $info['label'],
                        'href' => $info['href'],
                        'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                        'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',
                    ));
                } else {
                    $fieldset->addField($id, $info['type'], array(
                        'name' => $id,
                        'label' => $info['label'],
                        'title' => $info['label'],
                        'value' => isset($info['value']) ? $info['value'] : $model->getData($id),
                        'text' => isset($info['text']) ? $info['text'] : $model->getData($id),
                        'after_element_html' => isset($info['after_element_html']) ? $info['after_element_html'] : '',

                    ));
                }
            }
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Currency_Exception
     */
    protected function loadFields()
    {
        $model = $this->_coreRegistry->registry('csmarketplace_current_transaction');
        $renderOrderDesc = $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid\Renderer\Orderdesc');
        $renderName = $this->_objectManager->get('Ced\CsMarketplace\Block\Adminhtml\Vorders\Grid\Renderer\Vendorname');
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
                        'base_amount' => array(
                            'label' => __('Amount'),
                            'value' => $this->_localeCurrency->getCurrency($model->getBaseCurrency())->toCurrency($model->getData('base_amount')),
                            'type' => 'label',
                        ),
                        'amount' => array(
                            'label' => '',
                            'value' => '[' . $this->_localeCurrency->getCurrency($model->getCurrency())->toCurrency($model->getData('amount')) . ']',
                            'type' => 'label',
                        ),
                        'base_fee' => array(
                            'label' => __('Adjustment Amount'),
                            'value' => $this->_localeCurrency->getCurrency($model->getBaseCurrency())->toCurrency($model->getData('base_fee')),
                            'type' => 'label',
                        ),
                        'fee' => array(
                            'label' => '',
                            'value' => '[' . $this->_localeCurrency->getCurrency($model->getCurrency())->toCurrency($model->getData('fee')) . ']',
                            'type' => 'label',
                        ),
                        'base_net_amount' => array(
                            'label' => __('Net Amount'),
                            'value' => $this->_localeCurrency->getCurrency($model->getBaseCurrency())->toCurrency($model->getData('base_net_amount')),
                            'type' => 'label',
                        ),
                        'net_amount' => array(
                            'label' => '',
                            'value' => '[' . $this->_localeCurrency->getCurrency($model->getCurrency())->toCurrency($model->getData('net_amount')) . ']',
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
                        'base_amount' => array(
                            'label' => __('Amount'),
                            'value' => $this->_localeCurrency->getCurrency($model->getBaseCurrency())->toCurrency($model->getData('base_amount')),
                            'type' => 'label',
                        ),
                        'base_fee' => array(
                            'label' => __('Adjustment Amount'),
                            'value' => $this->_localeCurrency->getCurrency($model->getBaseCurrency())->toCurrency($model->getData('base_fee')),
                            'type' => 'label',
                        ),
                        'base_net_amount' => array(
                            'label' => __('Net Amount'),
                            'value' => $this->_localeCurrency->getCurrency($model->getBaseCurrency())->toCurrency($model->getData('base_net_amount')),
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
}