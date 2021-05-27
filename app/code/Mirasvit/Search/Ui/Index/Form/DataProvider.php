<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.100
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Ui\Index\Form;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\Search\Api\Data\IndexInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    /**
     * @var UiComponentFactory
     */
    private $uiComponentFactory;

    /**
     * @var ContextInterface
     */
    private $context;

    public function __construct(
        IndexRepositoryInterface $indexRepository,
        UiComponentFactory $uiComponentFactory,
        ContextInterface $context,
        //        DataInterfaceFactory $configFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        $this->context = $context;
//        $this->configFactory = $configFactory;

        $this->indexRepository = $indexRepository;
        $this->collection = $this->indexRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $id = $this->context->getRequestParam($this->getRequestFieldName(), null);
        if ($id) {
            $index = $this->indexRepository->get($id);

            $identifier = 'search_index_form_' . $index->getIdentifier();

            if (CompatibilityService::is22()) {
                $componentData = CompatibilityService::getObjectManager()
                    ->create('Magento\Ui\Config\DataFactory')
                    ->create(['componentName' => $identifier])->get($identifier);
            } else {
                $componentData = true;
            }

            try {
                if ($componentData) {
                    $component = $this->uiComponentFactory->create($identifier);
                    return ['props' => $this->prepareComponent($component)];
                }
            } catch (\Exception $e) {
                //file not exist
            }
        }

        return parent::getMeta();
    }

    /**
     * @param UiComponentInterface $component
     * @return array
     */
    protected function prepareComponent(UiComponentInterface $component)
    {
        $data = [];
        foreach ($component->getChildComponents() as $child) {
            $data['children'][] = $this->prepareComponent($child);
        }
        $component->prepare();
        $data['arguments']['data'] = $component->getData();

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->indexRepository->getCollection() as $index) {
            $instance = $this->indexRepository->getInstance($index);
            $attributes = $instance->getAttributeWeights();

            if (count($attributes) == 0) {
                $attributes['empty'] = 1; // for correct js type casting
            }

            $properties = $index->getProperties();
            if (count($properties) == 0) {
                $properties['empty'] = 1; // for correct js type casting
            }

            $data = [
                IndexInterface::ID         => $index->getId(),
                IndexInterface::TITLE      => $index->getTitle(),
                IndexInterface::IDENTIFIER => $index->getIdentifier(),
                IndexInterface::IS_ACTIVE  => $index->getIsActive(),
                IndexInterface::POSITION   => $index->getPosition(),
                'attributes'               => $attributes,
                'properties'               => $properties,
            ];

            $result[$index->getId()] = $data;
        }

        return $result;
    }
}
