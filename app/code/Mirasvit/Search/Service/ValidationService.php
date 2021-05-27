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



namespace Mirasvit\Search\Service;

use Mirasvit\Core\Service\AbstractValidator;
use Magento\Framework\Module\Manager;
use Magento\Framework\Module\ModuleListInterface;

class ValidationService extends AbstractValidator
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    public function __construct(
        Manager $moduleManager,
        ModuleListInterface $moduleList
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleList = $moduleList;
    }

    public function testKnownConflicts()
    {
        $known = ['Mageworks_SearchSuite', 'Magento_Solr', 'Magento_ElasticSearch'];

        foreach ($known as $moduleName) {
            if ($this->moduleManager->isEnabled($moduleName)) {
                $this->addError('Please disable {0} module.', [$moduleName]);
            }
        }
    }

    public function testPossibleConflicts()
    {
        $exceptions = ['Magento_Search', 'Magento_CatalogSearch'];

        foreach ($this->moduleList->getAll() as $module) {
            $moduleName = $module['name'];

            if (in_array($moduleName, $exceptions)) {
                continue;
            }

            if (stripos($moduleName, 'mirasvit') !== false) {
                continue;
            }

            if (stripos($moduleName, 'search') !== false && $this->moduleManager->isEnabled($moduleName)) {
                $this->addWarning("Possible conflict with {0} module.", [$moduleName]);
            }
        }
    }
}
