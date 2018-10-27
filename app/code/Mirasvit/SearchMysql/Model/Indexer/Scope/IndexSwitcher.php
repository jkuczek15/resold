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
 * @package   mirasvit/module-search-mysql
 * @version   1.0.25
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchMysql\Model\Indexer\Scope;

use Mirasvit\Core\Service\CompatibilityService;

if (CompatibilityService::is22()) {
    class ParentClass extends \Magento\CatalogSearch\Model\Indexer\Scope\IndexSwitcher
    {

    }
} else {
    class ParentClass
    {

    }
}

class IndexSwitcher extends ParentClass
{
}