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

use Magento\Framework\Registry;
use Mirasvit\SearchElastic\Model\Engine;

// @codingStandardsIgnoreStart
if (class_exists('Magento\CatalogSearch\Model\Indexer\Scope\State')) {
    class State extends \Magento\CatalogSearch\Model\Indexer\Scope\State
    {
    }
} else {
    class State
    {
    }
}

class IndexSwitcher
{
    /**
     * @var ScopeProxy
     */
    private $resolver;

    /**
     * @var State
     */
    private $state;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Engine
     */
    private $engine;

    public function __construct(
        ScopeProxy $indexScopeResolver,
        State $state,
        Registry $registry,
        Engine $engine
    ) {
        $this->resolver = $indexScopeResolver;
        $this->state = $state;
        $this->registry = $registry;
        $this->engine = $engine;
    }

    public function switchIndex(array $dimensions)
    {
        $indexName = $this->registry->registry(IndexerHandler::ACTIVE_INDEX);

        if (State::USE_TEMPORARY_INDEX === $this->state->getState()) {
            $tempIndexName = $this->resolver->resolve($indexName, $dimensions);

            $this->state->useRegularIndex();

            $regularIndexName = $this->resolver->resolve($indexName, $dimensions);

            $this->engine->moveIndex($tempIndexName, $regularIndexName);

            $this->state->useTemporaryIndex();
        }
    }
}
// @codingStandardsIgnoreEnd
