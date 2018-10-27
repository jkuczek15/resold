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
 * @package   mirasvit/module-search-elastic
 * @version   1.2.26
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Model\Indexer;

use Magento\Framework\Search\Request\Dimension;

class ScopeProxy implements \Magento\Framework\Search\Request\IndexScopeResolverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $states = [];

    private $scopeState = null;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $scopeState = null,
        $states = null
    ) {
        $this->objectManager = $objectManager;

        if ($states) { //m2.2
            $this->states = $states;
            $this->scopeState = $scopeState;
        } else { //m2.1
            $this->states = $scopeState;
        }
    }

    /**
     * Creates class instance with specified parameters
     *
     * @param string $state
     * @return \Magento\Framework\Search\Request\IndexScopeResolverInterface
     */
    private function create($state)
    {
        return $this->objectManager->create($this->states[$state]);
    }

    /**
     * @param string $index
     * @param Dimension[] $dimensions
     * @return string
     */
    public function resolve($index, array $dimensions)
    {
        $state = $this->scopeState ? $this->scopeState->getState() : 'use_main_table';

        return $this->create($state)->resolve($index, $dimensions);
    }
}
