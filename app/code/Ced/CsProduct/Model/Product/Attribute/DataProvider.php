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
namespace Ced\CsProduct\Model\Product\Attribute;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Text;

/**
 * Data provider for the form of adding new product attribute.
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreRepositoryInterface $storeRepository
     * @param ArrayManager $arrayManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
    		//PoolInterface $pool,
        StoreRepositoryInterface $storeRepository,
        ArrayManager $arrayManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->storeRepository = $storeRepository;
        $this->arrayManager = $arrayManager;
        //$this->pool = $pool;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
    	
        return [];
    }

    /**
     * Get meta information
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        $meta = $this->customizeAttributeCode($meta);
        $meta = $this->customizeFrontendLabels($meta);
        $meta = $this->customizeOptions($meta);

        return $meta;
    }

    /**
     * Customize attribute_code field
     *
     * @param array $meta
     * @return array
     */
    private function customizeAttributeCode($meta)
    {
        $meta['advanced_fieldset']['children'] = $this->arrayManager->set(
            'attribute_code/arguments/data/config',
            [],
            [
                'notice' => __(
                    'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                    EavAttribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'validation' => [
                    'max_text_length' => EavAttribute::ATTRIBUTE_CODE_MAX_LENGTH
                ]
            ]
        );
        return $meta;
    } 

    /**
     * Customize frontend labels
     *
     * @param array $meta
     * @return array
     */
     private function customizeFrontendLabels($meta)
    {
        foreach ($this->storeRepository->getList() as $store) {
            $storeId = $store->getId();

            if (!$storeId) {
                continue;
            }

            $meta['manage-titles']['children'] = [
                'frontend_label[' . $storeId . ']' => $this->arrayManager->set(
                    'arguments/data/config',
                    [],
                    [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'label' => $store->getName(),
                        'dataType' => Text::NAME,
                        'dataScope' => 'frontend_label[' . $storeId . ']'
                    ]
                ),
            ];
        }
        return $meta;
    } 

    /**
     * Customize options
     *
     * @param array $meta
     * @return array
     */
     private function customizeOptions($meta)
    {
        $sortOrder = 1;
        foreach ($this->storeRepository->getList() as $store) {
            $storeId = $store->getId();

            $meta['attribute_options_select_container']['children']['attribute_options_select']['children']
            ['record']['children']['value_option_' . $storeId] = $this->arrayManager->set(
                'arguments/data/config',
                [],
                [
                    'dataType' => 'text',
                    'formElement' => 'input',
                    'component' => 'Magento_Catalog/js/form/element/input',
                    'template' => 'Magento_Catalog/form/element/input',
                    'prefixName' => 'option.value',
                    'prefixElementName' => 'option_',
                    'suffixName' => (string)$storeId,
                    'label' => $store->getName(),
                    'sortOrder' => $sortOrder,
                    'componentType' => Field::NAME,
                ]
            );
            $meta['attribute_options_multiselect_container']['children']['attribute_options_multiselect']['children']
            ['record']['children']['value_option_' . $storeId] = $this->arrayManager->set(
                'arguments/data/config',
                [],
                [
                    'dataType' => 'text',
                    'formElement' => 'input',
                    'component' => 'Magento_Catalog/js/form/element/input',
                    'template' => 'Magento_Catalog/form/element/input',
                    'prefixName' => 'option.value',
                    'prefixElementName' => 'option_',
                    'suffixName' => (string)$storeId,
                    'label' => $store->getName(),
                    'sortOrder' => $sortOrder,
                    'componentType' => Field::NAME,
                ]
            );
            ++$sortOrder;
        }

        $meta['attribute_options_select_container']['children']['attribute_options_select']['children']
        ['record']['children']['action_delete'] = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'componentType' => 'actionDelete',
                'dataType' => 'text',
                'fit' => true,
                'sortOrder' => $sortOrder,
                'component' => 'Magento_Catalog/js/form/element/action-delete',
                'elementTmpl' => 'Magento_Catalog/form/element/action-delete',
                'template' => 'Magento_Catalog/form/element/action-delete',
                'prefixName' => 'option.delete',
                'prefixElementName' => 'option_',
            ]
        );
        $meta['attribute_options_multiselect_container']['children']['attribute_options_multiselect']['children']
        ['record']['children']['action_delete'] = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'componentType' => 'actionDelete',
                'dataType' => 'text',
                'fit' => true,
                'sortOrder' => $sortOrder,
                'component' => 'Magento_Catalog/js/form/element/action-delete',
                'elementTmpl' => 'Magento_Catalog/form/element/action-delete',
                'template' => 'Magento_Catalog/form/element/action-delete',
                'prefixName' => 'option.delete',
                'prefixElementName' => 'option_',
            ]
        );
        return $meta;
    } 
}
