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



namespace Mirasvit\SearchElastic\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Locale\Resolver as LocaleResolver;

class Config
{
    /**
     * @var LocaleResolver
     */
    private $localeResolver;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    const DOCUMENT_TYPE = 'doc';

    public function __construct(
        LocaleResolver $localeResolver,
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem
    ) {
        $this->localeResolver = $localeResolver;
        $this->scopeConfig = $scopeConfig;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return $this->scopeConfig->getValue('search/engine/engine');
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->scopeConfig->getValue('search/engine/elastic_host');
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return intval($this->scopeConfig->getValue('search/engine/elastic_port'));
    }

    /**
     * @return string
     */
    public function getIndexPrefix()
    {
        return strtolower($this->scopeConfig->getValue('search/engine/elastic_prefix'));
    }

    /**
     * @param string $indexName
     * @return string
     */
    public function getIndexName($indexName)
    {

        return $this->getIndexPrefix() . '_' . $indexName;
    }

    /**
     * @return bool
     */
    public function isShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag('cataloginventory/options/show_out_of_stock');
    }

    /**
     * @return bool
     */
    public function isFastMode()
    {
        return $this->scopeConfig->isSetFlag('searchautocomplete/general/fast_mode');
    }

    /**
     * @param Dimension $dimension
     * @return string
     */
    public function getStemmer(Dimension $dimension)
    {
        $supported = [
            'ar' => 'arabic',
            'hy' => 'armenian',
            'bn' => 'bengali',
            'br' => 'brazilian',
            'bg' => 'bulgarian',
            'ca' => 'catalan',
            'cs' => 'czech',
            'da' => 'danish',
            'nl' => 'dutch',
            'fi' => 'finnish',
            'el' => 'greek',
            'hu' => 'hungarian',
            'it' => 'italian',
            'lv' => 'latvian',
            'lt' => 'lithuanian',
            'nb' => 'norwegian',
            'nn' => 'norwegian',
            'pt' => 'portuguese',
            'es' => 'spanish',
            'sv' => 'swedish',
            'en' => 'english',
            'de' => 'german',
            'fr' => 'french',
            'ru' => 'russian',
        ];

        $this->localeResolver->emulate($dimension->getValue());
        $locale = strtolower(explode('_', $this->localeResolver->getLocale())[0]);

        return isset($supported[$locale]) ? $supported[$locale] : 'english';
    }
}
