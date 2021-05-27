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
 * @license     http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Helper\Tool;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Ced\CsMarketplace\Helper\Data
{

    /**
     * Vendor
     *
     * @var \Ced\CsMarketplace\Model\Vendor
     */
    protected $_vendor;

    public $_assetRepo;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        parent::__construct($context, $objectManager, $cacheTypeList, $cacheFrontendPool, $request, $productMetadata);
        $this->_assetRepo = $assetRepo;
    }

    public function getResizeImage($image, $attr, $width = null, $height = null)
    {
        
        $this->_imageFactory = $this->_objectManager->create('\Magento\Framework\Image\AdapterFactory');
        $this->_mediaDirectory = $this->_objectManager->get('\Magento\Framework\Filesystem')->getDirectoryWrite(DirectoryList::MEDIA);
        $absolutePath = $this->_mediaDirectory->getAbsolutePath() . $image;

        if (!is_file($absolutePath)) {
            if ($attr == "logo" && $this->getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder', $this->_storeManager->getStore(null)->getId())) {
                $imgpath = $this->getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder', $this->_storeManager->getStore(null)->getId());
                $image = "/ced/csmarketplace/".$imgpath;
                $absolutePath = $this->_mediaDirectory->getAbsolutePath() . $image;
            } elseif ($attr == "banner" && $this->getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder', $this->_storeManager->getStore(null)->getId())) {
                $imgpath = $this->getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder', $this->_storeManager->getStore(null)->getId());
                $image = "/ced/csmarketplace/".$imgpath;
                $absolutePath = $this->_mediaDirectory->getAbsolutePath() . $image;
            } else {
                $image = 'Ced_CsMarketplace::images/ced/csmarketplace/vendor/placeholder/'.$attr.'.jpg';
                return $this->getViewFileUrl($image);
            }
        }

        $path = 'catalog/product/cache';
        if ($width !== null) {
            $path .= '/' . $width . 'x';
            if ($height !== null) {
                $path .= $height;
            }
        } else {
            return $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $image;
        }

        $finalPathToWrite = $path . '/' . $attr . '/' . $image;
        $imageResized = $this->_mediaDirectory->getAbsolutePath($finalPathToWrite);

        if (!$this->_mediaDirectory->isFile($finalPathToWrite)) {
            $imageFactory = $this->_imageFactory->create();
            $imageFactory->open($absolutePath);
            $imageFactory->quality(100);
            $imageFactory->constrainOnly(true);
            $imageFactory->keepAspectRatio(true);
            $imageFactory->keepFrame(true);
            $imageFactory->keepTransparency(true);
            $imageFactory->backgroundColor([255, 255, 255]);
            $imageFactory->resize($width, $height);
            $imageFactory->save($imageResized);
        }
        return $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $finalPathToWrite;
    }

    public function getViewFileUrl($fileId)
    {
        try {
            $params = ['_secure' => $this->_request->isSecure()];
            return $this->_assetRepo->getUrlWithParams($fileId, $params);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return False;
        }
    }

    /**
     * Set current Vendor
     *
     * @param  \Ced\CsMarketplace\Model\Vendor\Image $vendor
     * @return \Ced\CsMarketplace\Helper\Tool\Image
     */
    protected function setVendor($vendor)
    {
        $this->_vendor = $vendor;
        return $this;
    }

    /**
     * Get current Vendor
     *
     * @return \Ced\CsMarketplace\Model\Vendor\Image
     */
    protected function getVendor()
    {
        return $this->_vendor;
    }

}
