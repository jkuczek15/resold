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
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Helper\Vproducts;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $_objectManager;

    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager
    ){
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * Save images to media gallery and set product default image
     *
     * @params Mage_Catalog_Model_Product $product, array $data
     */
    public function saveImages($product, $data)
    {
        $defaultimage = '';
        $productid = $product->getId();
        $productModel = clone $product;
        if ($productModel && $productModel->getId()) {
            if (isset($data['real_img_val']) && count($data['real_img_val']) > 0) {
                foreach ($data['real_img_val'] as $key => $value) {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => "images[{$key}]"]);
                    $file_data = $uploader->validateFile();
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                    $targetDir = $mediaDirectory->getAbsolutePath('ced/csmaketplace/vproducts/' . $productid);
                    $image = md5($file_data['tmp_name']) . $file_data['name'];
                    try {
                        if ($result = $uploader->save($targetDir, $image)) {
                            $fetchTarget = $targetDir . '/' . $result['file'];
                            $productModel->addImageToMediaGallery(
                                    $fetchTarget, array(
                                    'image',
                                    'small_image',
                                    'thumbnail'
                                ), true, false
                            );
                            if (isset($data ['defaultimage']) && $data ['defaultimage'] != '') {
                                if ($data ['defaultimage'] == "real_img_val[{$key}]") {
                                    $defaultimage = $result['file'];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
                    }
                }
            }
            $productModel->save();

            if (isset($data ['defaultimage']) && $data ['defaultimage'] != '') {
                if ($defaultimage == '') {
                    $defaultimage = $data ['defaultimage'];
                }
                if ($defaultimage !== '') {
                    $mediaGallery = $productModel->getMediaGallery();
                    if (isset($mediaGallery['images'])) {
                        foreach ($mediaGallery['images'] as $image) {
                            if (strpos($image['file'], $defaultimage) !== false) {
                                $this->_objectManager->get('Magento\Catalog\Model\Product\Action')
                                    ->updateAttributes([$productid], ['image' => $image['file'], 'small_image' => $image['file'], 'thumbnail' => $image['file']], $product->getStoreId());
                                break;
                            }
                        }
                    }
                }
            } else {
                $this->_objectManager->get('Magento\Catalog\Model\Product\Action')
                    ->updateAttributes([$productid],
                        ['image' => '', 'small_image' => '', 'thumbnail' => ''], $product->getStoreId());
            }
        }
    }
}
