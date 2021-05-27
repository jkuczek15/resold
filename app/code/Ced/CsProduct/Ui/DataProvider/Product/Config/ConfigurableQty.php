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
namespace Ced\CsProduct\Ui\DataProvider\Product\Config;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Data provider for quantity in the Configurable products
 */
class ConfigurableQty extends AbstractModifier
{
    const CODE_QUANTITY = 'qty';
    const CODE_QTY_CONTAINER = 'quantity_and_stock_status_qty';

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($groupCode = $this->getGroupCodeByField($meta, self::CODE_QTY_CONTAINER)) {
            $parentChildren = &$meta[$groupCode]['children'];
            if (!empty($parentChildren[self::CODE_QTY_CONTAINER])) {
                $parentChildren[self::CODE_QTY_CONTAINER] = array_replace_recursive(
                    $parentChildren[self::CODE_QTY_CONTAINER],
                    [
                        'children' => [
                            self::CODE_QUANTITY => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'imports' => [
                                                'disabled' => '!ns = ${ $.ns }, index = '
                                                    . ConfigurablePanel::CONFIGURABLE_MATRIX . ':isEmpty',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]
                );
            }
        }

        return $meta;
    }
}
