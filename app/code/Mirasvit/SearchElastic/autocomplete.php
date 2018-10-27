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



use Mirasvit\SearchElastic\Model\Engine;
use Elasticsearch\ClientBuilder;

if (php_sapi_name() == "cli") {
    return;
}

$configFile = BP . '/app/etc/autocomplete.json';
if (!file_exists($configFile)) {
    return;
}

$config = \Zend_Json::decode(file_get_contents($configFile));

if ($config['engine'] !== 'elastic') {
    return;
}

class ElasticAutocomplete
{
    private $config;

    public function __construct(
        array $config
    ) {
        $this->config = $config;
    }

    public function process()
    {
        $result = [];
        $totalItems = 0;

        foreach ($this->config['indexes'] as $identifier => $config) {
            $query = [
                'index' => $config['index'],
                'type'  => 'doc',
                'size'  => $config['limit'],
                'body'  => [
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'query_string' => [
                                        'fields' => $this->getWeights($identifier),
                                        'query'  => $this->getQuery(),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            try {
                $response = $this->getClient()->search($query);
                $total = $response['hits']['total'];
                $items = $this->mapHits($response['hits']['hits'], $config);

                if ($total && $items) {
                    $result['indices'][] = [
                        'identifier'   => $identifier == 'catalogsearch_fulltext' ? 'magento_catalog_product' : $identifier,
                        'isShowTotals' => true,
                        'order'        => $config['order'],
                        'title'        => $config['title'],
                        'totalItems'   => $total,
                        'items'        => $items,
                    ];
                    $totalItems += $total;
                }
            } catch (\Exception $e) {
            }
        }

        $result['query'] = $this->getQueryText();
        $result['totalItems'] = $totalItems;
        $result['noResults'] = $totalItems == 0;
        $result['textEmpty'] = sprintf($this->config['textEmpty'], $this->getQueryText());
        $result['textAll'] = sprintf($this->config['textAll'], $result['totalItems']);
        $result['urlAll'] = $this->config['urlAll'] . $this->getQueryText();

        return $result;
    }

    private function getClient()
    {
        $client = ClientBuilder::fromConfig([
            'hosts' => [$this->config['host'] . ':' . $this->config['port']],
        ]);

        return $client;
    }

    private function getWeights($identifier)
    {
        $weights = [
            'options^1',
        ];
        foreach ($this->config['indexes'][$identifier]['fields'] as $f => $w) {
            $weights[] = $f . '^' . pow(2, $w);
        }

        return $weights;
    }

    private function getQueryText()
    {
        return isset($_GET['q']) ? $_GET['q'] : '';
    }

    private function getQuery()
    {
        $terms = array_filter(explode(" ", $this->getQueryText()));

        $conditions = [];
        foreach ($terms as $term) {
            $term = $this->escape($term);
            $conditions[] = "($term OR *$term*)";
        }

        return implode(" AND ", $conditions);
    }

    private function escape($value)
    {
        $pattern = '/(\+|-|\/|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $value);
    }

    private function mapHits($hits, $config)
    {
        $items = [];
        foreach ($hits as $hit) {
            if (count($items) > $config['limit']) {
                break;
            }

            if (!isset($hit['_source'])
                || !isset($hit['_source']['autocomplete'])
                || !is_array($hit['_source']['autocomplete'])) {
                continue;
            }

            $item = [
                'title'       => null,
                'url'         => null,
                'sku'         => null,
                'image'       => null,
                'description' => null,
                'price'       => null,
                'rating'      => null,
            ];

            $item = array_merge($item, $hit['_source']['autocomplete']);

            $item['cart'] = [
                'visible' => false,
                'params'  => [
                    'action' => null,
                    'data'   => [
                        'product' => null,
                        'uenc'    => null,
                    ],
                ],
            ];

            if (!isset($item['name']) || !$item['name']) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }
}

$result = (new \ElasticAutocomplete($config))->process();

echo \Zend_Json::encode($result);
die();
