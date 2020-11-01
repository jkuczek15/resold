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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.65
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Service;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\SearchAutocomplete\Api\Repository\IndexRepositoryInterface;
use Mirasvit\SearchAutocomplete\Model\Config;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;

class JsonConfigService
{
    const AUTOCOMPLETE = 'autocomplete';

    const TYPEAHEAD = 'typeahead';

    private $fs;

    private $scopeConfig;

    private $config;

    private $indexRepository;

    private $searchHelper;

    private $queryCollectionFactory;

    public function __construct(
        Filesystem $fs,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        IndexRepositoryInterface $indexRepository,
        SearchHelper $searchHelper,
        QueryCollectionFactory $queryCollectionFactory
    ) {
        $this->fs = $fs;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->indexRepository = $indexRepository;
        $this->searchHelper = $searchHelper;
        $this->queryCollectionFactory = $queryCollectionFactory;
    }

    /**
     * @return $this
     */
    public function ensure($option)
    {
        $path = $this->fs->getDirectoryRead(DirectoryList::CONFIG)->getAbsolutePath();
        $filePath = $path . $option . '.json';

        if (!$this->isOptionEnabled($option)) {
            @unlink($filePath);

            return $this;
        }

        $config = $this->generate($option);

        @file_put_contents($filePath, \Zend_Json::encode($config));

        return $this;
    }

    /**
     * @return array
     */
    public function generate($option)
    {
        switch ($option) {
            case self::AUTOCOMPLETE:
                return $this->generateAutocompleteConfig();
                break;
            case self::TYPEAHEAD:
                return $this->generateTypeaheadConfig();
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @return bool
     */
    private function isOptionEnabled($option)
    {
        switch ($option) {
            case self::AUTOCOMPLETE:
                return $this->config->isFastMode();
                break;
            case self::TYPEAHEAD:
                return $this->config->isTypeAheadEnabled();
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @return array
     */
    private function generateAutocompleteConfig()
    {
        $config = [
            'engine'                    => $this->scopeConfig->getValue('search/engine/engine'),
            'is_optimize_mobile'        => $this->config->isOptimizeMobile(),
            'is_show_cart_button'       => $this->config->isShowCartButton(),
            'is_show_image'             => $this->config->isShowImage(),
            'is_show_price'             => $this->config->isShowPrice(),
            'is_show_rating'            => $this->config->isShowRating(),
            'is_show_sku'               => $this->config->isShowSku(),
            'is_show_short_description' => $this->config->isShowShortDescription(),
            'textAll'                   => __('Show all %1 results →', "%s")->render(),
            'textEmpty'                 => __('Sorry, nothing found for "%1".', "%s")->render(),
            'urlAll'                    => $this->searchHelper->getResultUrl(""),
        ];

        foreach ($this->indexRepository->getIndices() as $index) {
            $identifier = $index->getIdentifier();

            if (!$this->config->getIndexOptionValue($identifier, 'is_active')) {
                continue;
            }

            if ($identifier == 'magento_catalog_categoryproduct' || $identifier == 'magento_search_query') {
                continue;
            }

            $index->addData($this->config->getIndexOptions($identifier));

            $config['indexes'][$identifier] = [
                'title'      => __($index->getTitle())->render(),
                'identifier' => $identifier,
                'order'      => $index->getOrder(),
                'limit'      => $index->getLimit(),
            ];
        }

        return $config;
    }

    /**
     * @return array
     */
    private function generateTypeaheadConfig()
    {
        $config = [];
        $config['engine'] = false;

        $collection = $this->queryCollectionFactory->create();

        $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS)
            ->columns([
                'suggest' => new \Zend_Db_Expr('MAX(query_text)'),
                'suggest_key' => new \Zend_Db_Expr('substring(query_text,1,2)'),
                'popularity' => new \Zend_Db_Expr('MAX(popularity)'),
            ])
            ->where('num_results > 0')
            ->where('display_in_terms = 1')
            ->where('is_active = 1')
            ->where('popularity > 10 ')
            ->where('CHAR_LENGTH(query_text) > 3')
            ->group(new \Zend_Db_Expr('substring(query_text,1,6)'))
            ->group(new \Zend_Db_Expr('substring(query_text,1,2)'))
            ->order('suggest_key '. \Magento\Framework\DB\Select::SQL_ASC)
            ->order('popularity ' . \Magento\Framework\DB\Select::SQL_DESC);

        foreach ($collection as $suggestion) {
            $config[strtolower($suggestion['suggest_key'])][] = strtolower($suggestion['suggest']);
        }

        return $config;
    }
}
