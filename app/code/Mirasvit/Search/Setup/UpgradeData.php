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


namespace Mirasvit\Search\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var BlockInterfaceFactory
     */
    protected $blockFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'search_weight',
                [
                    'type'                  => 'static',
                    'label'                 => 'Search Weight',
                    'input'                 => 'text',
                    'required'              => false,
                    'sort_order'            => 1000,
                    'global'                => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group'                 => 'Product Details',
                    'is_used_in_grid'       => false,
                    'is_visible_in_grid'    => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            /** @var \Magento\Cms\Api\Data\BlockInterface $block */
            $block = $this->blockFactory->create();

            $block->setIdentifier('no-results')
                ->setTitle('Search: No Results Suggestions')
                ->setContent($this->getBlockContent('no_results'))
                ->setIsActive(true);

            $this->blockRepository->save($block);
        }

        if (version_compare($context->getVersion(), '1.0.7') < 0) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                'sold_qty',
                [
                    'type'                  => 'decimal',
                    'label'                 => 'Sold QTY',
                    'input'                 => 'text',
                    'required'              => false,
                    'sort_order'            => 1005,
                    'global'                => ScopedAttributeInterface::SCOPE_STORE,
                    'group'                 => false,
                    'visible'               => false,
                    'is_used_in_grid'       => false,
                    'is_system'             => false,
                    'is_visible_in_grid'    => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }

        $setup->endSetup();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getBlockContent($name)
    {
        return file_get_contents(dirname(__FILE__) . "/data/$name.html");
    }
}
