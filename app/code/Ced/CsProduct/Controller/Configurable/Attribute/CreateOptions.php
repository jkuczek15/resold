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
 namespace Ced\CsProduct\Controller\Configurable\Attribute;

use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Framework\App\Action;

class CreateOptions extends Action\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_CsProduct::attributes_attributes';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param AttributeFactory $attributeFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        AttributeFactory $attributeFactory
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->attributeFactory = $attributeFactory;
        parent::__construct($context);
    }

    /**
     * Search for attributes by part of attribute's label in admin store
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->representJson($this->jsonHelper->jsonEncode($this->saveAttributeOptions()));
    }

    /**
     * Save attribute options just created by user
     *
     * @return array
     * @TODO Move this logic to configurable product type model
     *   when full set of operations for attribute options during
     *   product creation will be implemented: edit labels, remove, reorder.
     * Currently only addition of options to end and removal of just added option is supported.
     */
    protected function saveAttributeOptions()
    {
        $options = (array)$this->getRequest()->getParam('options');
        $savedOptions = [];
        foreach ($options as $option) {
            if (isset($option['label']) && isset($option['is_new'])) {
                $attribute = $this->attributeFactory->create();
                $attribute->load($option['attribute_id']);
                $optionsBefore = $attribute->getSource()->getAllOptions();
                $attribute->setOption(
                    [
                        'value' => ['option_0' => [$option['label']]],
                        'order' => ['option_0' => count($optionsBefore) + 1],
                    ]
                );
                $attribute->save();
                $attribute = $this->attributeFactory->create();
                $attribute->load($option['attribute_id']);
                $optionsAfter = $attribute->getSource()->getAllOptions();
                $newOption = array_pop($optionsAfter);
                $savedOptions[$option['id']] = $newOption['value'];
            }
        }
        return $savedOptions;
    }
}
