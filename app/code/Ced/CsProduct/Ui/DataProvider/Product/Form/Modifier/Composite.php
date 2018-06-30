<?php
 /**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProduct\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\Catalog\Model\Product\Type as CatalogType;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\ModifierFactory;

/**
 * Customize Downloadable panel
 */
class Composite extends \Magento\Downloadable\Ui\DataProvider\Product\Form\Modifier\Composite
{

    const CHILDREN_PATH = 'downloadable/children';
    const CONTAINER_LINKS = 'container_links';
    const CONTAINER_SAMPLES = 'container_samples';

    /**
     * @var ModifierFactory
     */
    protected $modifierFactory;

    /**
     * @var array
     */
    protected $modifiers = [];

    /**
     * @var ModifierInterface[]
     */
    protected $modifiersInstances = [];

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @param ModifierFactory $modifierFactory
     * @param LocatorInterface $locator
     * @param array $modifiers
     */
    public function __construct(
        ModifierFactory $modifierFactory,
        LocatorInterface $locator,
        array $modifiers = []
    ) {
    	parent::__construct($modifierFactory, $locator);
        $this->modifierFactory = $modifierFactory;
        $this->locator = $locator;
        $this->modifiers = $modifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->canShowDownloadablePanel()) {
            foreach ($this->getModifiers() as $modifier) {
                $data = $modifier->modifyData($data);
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->canShowDownloadablePanel()) {
            foreach ($this->getModifiers() as $modifier) {
                $meta = $modifier->modifyMeta($meta);
            }
        }

        return $meta;
    }

    /**
     * Check that can show downloadable panel
     *
     * @return bool
     */
    protected function canShowDownloadablePanel()
    {
        $productTypes = [
            DownloadableType::TYPE_DOWNLOADABLE,
            CatalogType::TYPE_SIMPLE,
            CatalogType::TYPE_VIRTUAL
        ];

        return in_array($this->locator->getProduct()->getTypeId(), $productTypes);
    }

    /**
     * Get modifiers list
     *
     * @return ModifierInterface[]
     */
    protected function getModifiers()
    {
        if (empty($this->modifiersInstances)) {
            foreach ($this->modifiers as $modifierClass) {
                $this->modifiersInstances[$modifierClass] = $this->modifierFactory->create($modifierClass);
            }
        }

        return $this->modifiersInstances;
    }
}
