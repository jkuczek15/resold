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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsProduct\Block\ConfigurableProduct\Product\Steps;

class SelectAttributes extends \Magento\Ui\Block\Component\StepsWizard\StepAbstract
{
/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry
    ) {
       parent::__construct($context);
        $this->registry = $registry;
    }

    /**
     * Get Add new Attribute button
     *
     * @param string $dataProvider
     * @return string
     */
      public function getAddNewAttributeButtons($dataProvider = '')
    {
        /** @var \Magento\Backend\Block\Widget\Button $attributeCreate */
        $attributeCreate = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        );
        $attributeCreate->setDataAttribute(
            [
                'mage-init' => [
                    'productAttributes' => [
                        'dataProvider' => $dataProvider,
                        'url' => $this->getUrl('csproduct/configurableproduct_attribute/new', [
                            'store' => $this->registry->registry('current_product')->getStoreId(),
                            'product_tab' => 'variations',
                            'popup' => 1,
                            '_query' => [
                                'attribute' => [
                                    'is_global' => 1,
                                    'frontend_input' => 'select',
                                ],
                            ],
                        ]),
                    ],
                ],
            ]
        )->setType(
            'button'
        )->setLabel(
            __('Create New Attribute')
        );
        return $attributeCreate->toHtml();
    }  

    /**
     * {@inheritdoc}
     */
    public function getCaption()
    {
        return __('Select Attributes');
    }
}
