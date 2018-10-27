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



namespace Mirasvit\Search\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;

use Mirasvit\Search\Model\Config;

class IndexTree implements ArrayInterface
{
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    public function __construct(
        IndexRepositoryInterface $indexRepository
    ) {
        $this->indexRepository = $indexRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $identifiers = $this->indexRepository->getCollection()->getColumnValues('identifier');

        foreach ($this->indexRepository->getList() as $instance) {
            if (in_array($instance->getIdentifier(), $identifiers)
                && in_array($instance->getIdentifier(), Config::DISALLOWED_MULTIPLE)) {
                continue;
            }

            $identifier = $instance->getIdentifier();
            $group = trim(explode('/', $instance->getName())[0]);
            $name = trim(explode('/', $instance->getName())[1]);

            if (!isset($options[$group])) {
                $options[$group] = [
                    'label'    => $group,
                    'value'    => $group,
                    'optgroup' => [],
                ];
            }

            $options[$group]['optgroup'][] = [
                'label' => (string)$name,
                'value' => $identifier,
            ];
        }

        return array_values($options);
    }
}
