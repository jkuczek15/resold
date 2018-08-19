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


namespace Ced\CsMessaging\Block\Adminhtml\Customer\Message\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Ced\CsMarketplace\Model\VendorFactory;
use Magento\Framework\Registry;
use Ced\CsMessaging\Helper\Data;

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
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\objectManagerInterface $objectManager,
        VendorFactory $vendorFactory,
        Registry $registry,
        Data $messagingHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $objectManager;
        $this->_vendorFactory = $vendorFactory->create();
        $this->_registry = $registry;
        $this->_messagingHelper = $messagingHelper;
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $customerIds = $this->_registry->registry('customer_ids');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $formValues = ['customer_ids'=>implode($customerIds, ',')];
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Compose Message')]);
        $fieldset->addField('customer_ids', 'hidden', ['name' => 'customer_ids']);

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


        $fieldset->addField(
            'subject',
            'text',
            [
                'name' => 'subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'message',
            'textarea',
            [
                'name' => 'message',
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
                    'label' => __('Email this message to Customers'),
                    'title' => __('Email this message to Customers'),
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
