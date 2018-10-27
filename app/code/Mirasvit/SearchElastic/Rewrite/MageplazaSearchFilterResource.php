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



namespace Mirasvit\SearchElastic\Rewrite;

use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;

if (class_exists('Manadev\ProductCollection\Contracts\FilterResource')) {
    abstract class ParentFilter extends \Manadev\ProductCollection\Contracts\FilterResource
    {

    }
} else {
    abstract class ParentFilter
    {

    }
}

class MageplazaSearchFilterResource extends ParentFilter
{
    public function _construct()
    {

    }

    public function apply(Select $select, \Manadev\ProductCollection\Contracts\Filter $filter, $callback)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Manadev\ProductCollection\Factory $factory */
        $factory = $om->create('Manadev\ProductCollection\Factory');
        /** @var \Mirasvit\SearchElastic\Adapter\ElasticAdapter $adapter */
        $adapter = $om->create('Mirasvit\SearchElastic\Adapter\ElasticAdapter');

        /** @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorage $storage */
        $storage = $om->create('Magento\Framework\Search\Adapter\Mysql\TemporaryStorage');

        $requestBuilder = $factory->createRequestBuilder();
        $requestBuilder->bindDimension('scope', $this->getStoreId());
        $requestBuilder->bind('search_term', $filter->getText());
        $requestBuilder->setRequestName('quick_search_container');
        $request = $requestBuilder->create();

        $response = $adapter->query($request);

        $table = $storage->storeApiDocuments($response);

        $select->joinInner(['search_result' => $table->getName()],
            'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID, []);
    }
}