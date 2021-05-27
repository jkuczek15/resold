<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_AmazonAffiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsMessaging\Block\Adminhtml\Message\Compose\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Ced\CsMarketplace\Model\VendorFactory;
use Ced\CsMessaging\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\objectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        objectManagerInterface $objectManager,
        VendorFactory $vendorFactory,
        Data $messagingHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $objectManager;
        $this->_messagingHelper = $messagingHelper;
        $this->_vendorFactory = $vendorFactory->create();

    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Compose Message')]);
        $fieldset->addField('id', 'hidden', ['name' => 'id']);

        $vendors = $this->_vendorFactory->getCollection()->getData();
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $vArray = [];
        if (count($vendors))
        {
            foreach ($vendors as $vendor)
            {
                $vendor=$this->_vendorFactory->load($vendor['entity_id']);
                $vArray[$vendor->getEntityId()] =$vendor->getName();
            }
        }

        $formValues = ['receiver'=>$vendorId];

        $fieldset->addField(
            'receiver',
            'select',
            [
                'name' => 'receiver',
                'label' => __('Select Seller'),
                'title' => __('Select Seller'),
                'options' => $vArray,
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'msg_subject',
            'text',
            [
                'name' => 'msg_subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'msg_content',
            'textarea',
            [
                'name' => 'msg_content',
                'label' => __('Message'),
                'title' => __('Message'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        if ($this->_messagingHelper->getScopeConfigValue(Data::CAN_ADMIN_SEND_MAIL)) {
            $fieldset->addField(
                'ismailsent',
                'checkboxes',
                [
                    'name' => 'ismailsent',
                    'label' => __('Email this message to Seller'),
                    'title' => __('Email this message to Seller'),
                    'disabled' => $isElementDisabled,
                    'values' => [['value' => '1', 'label' => __('Yes')]],
                ]
            );
        }

        $form->setValues($formValues);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Compose');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Compose');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
