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
 * @category  Ced
 * @package   Ced_Facebook
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Facebook\Block\Adminhtml\Product;

class ListProduct extends /*\Magento\Catalog\Block\Product\ListProduct*/\Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $objectManager;

    /**
     * ListProduct constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {  
        parent::__construct($context, $data);
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @return mixed
     */
    protected function _getProductCollection()
    {
        return $this->objectManager->create('\Magento\Catalog\Model\Product')->getCollection();
    }

    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->objectManager->create('\Magento\Catalog\Block\Product\ImageBuilder')
            ->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }



}
