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

namespace Ced\CsMarketplace\Helper\Vproducts;

class Link extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_objectManager = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     *  Upload Downloadable product data
     * @param $type
     * @param $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadDownloadableFiles($type, $data)
    {
        $mediaDirectory = $this->_objectManager->create('Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $uploadDir = $mediaDirectory->getAbsolutePath("downloadable/files/" . $type . "/");

        if ($type == "samples") {
            $formats = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/downloadable_config/sample_formats');
        } else if ($type == "link_samples") {
            $formats = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/downloadable_config/sample_formats');
        } else if ($type == "links") {
            $formats = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vproducts/downloadable_config/link_formats');
        }
        $tempArr = explode(',', $formats);

        $formats_array = [];
        foreach ($tempArr as $value) {
            if (strlen($value)) {
                $formats_array [] = trim($value);
            }
        }

        $uploaded_files_array = [];
        $uploader = '';
        if (isset($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                if ($type == "link_samples") {
                    if (isset($data [$key] ['sample']['type']) && $data [$key] ['sample']['type'] == "url") {
                        continue;
                    }
                } else {
                    if (isset($data [$key] ['type']) && $data [$key] ['type'] == "url") {
                        continue;
                    }
                }
                try {
                    $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => "{$type}[{$key}]"]);
                } catch (\Exception $e) {
                    // return;                  
                }
                if ($uploader) {
                    $file_data = $uploader->validateFile();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowedExtensions($formats_array);
                    $file = $file_data ['name'];
                    try {
                        if ($result = $uploader->save($uploadDir, $file)) {
                            $uploaded_files_array [$key] = $result ['file'];
                        }
                    } catch (\Exception $e) {
                        throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
                    }
                }
            }
        }
        return $uploaded_files_array;
    }

    /**
     * Helper for saving downlodable product samples data
     *
     * @params array $data,array $samples
     */
    public function processSamplesData($samplesdata, $samples, $productid)
    {
        if (is_array($samplesdata) && count($samplesdata) > 0) {
            // setting sample data
            foreach ($samplesdata as $key => $val) {
                $linkModel = null;
                if ($samplesdata[$key]['sample_id'] != '') {
                    $linkModel = $this->_objectManager->create('Magento\Downloadable\Model\Sample')->load($samplesdata[$key]['sample_id']);
                } else if ($samplesdata[$key]['sample_id'] == '') {
                    $linkModel = $this->_objectManager->create('Magento\Downloadable\Model\Sample');
                    $linkModel->setProductId($productid);
                    $linkModel->setStoreId(0);
                    $linkModel->setWebsiteId(0);
                }
                $linkModel->setSortOrder(isset($samplesdata[$key]['sort_order']) ? $samplesdata[$key]['sort_order'] : 0);
                $linkModel->setTitle(isset($samplesdata[$key]['title']) ? $samplesdata[$key]['title'] : '');

                // /setting sample file
                if (isset($samplesdata[$key]['type']) && ($samplesdata[$key]['type'] == 'file')) {
                    if (isset($samples[$key])) {
                        $linkModel->setSampleFile("/" . $samples [$key]);
                    }
                    $linkModel->setSampleType("file");
                } else if (isset($samplesdata[$key]['type']) && ($samplesdata[$key]['type'] == 'url')) {
                    $linkModel->setSampleType("url");
                    if (isset($samplesdata[$key]['sample_url'])) {
                        $linkModel->setSampleUrl($samplesdata[$key]['sample_url']);
                    }
                }
                $linkModel->save();
            }
        }
    }

    /**
     * Helper for saving downlodable product Links data
     *
     * @params array $data,array $samples
     */
    public function processLinksData($linksdata, $links, $link_samples, $productid)
    {
        // //setting link data
        $helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
        if ($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store')) {
            $currentStoreId = $this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store');
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->setCurrentStore($currentStoreId);
        }

        if (is_array($linksdata) && count($linksdata) > 0) {
            foreach ($linksdata as $key => $val) {
                $linkModel = null;
                if (isset($linksdata[$key]['link_id']) && $linksdata[$key]['link_id'] != '') {
                    $linkModel = $this->_objectManager->create('Magento\Downloadable\Model\Link')->load($linksdata[$key]['link_id']);
                } else if (isset($linksdata[$key]['link_id']) && $linksdata[$key]['link_id'] == '') {
                    $linkModel = $this->_objectManager->create('Magento\Downloadable\Model\Link');
                    $linkModel->setProductId($productid);
                    $linkModel->setStoreId(0);
                    $linkModel->setWebsiteId(0);
                    $linkModel->setProductWebsiteIds($this->_objectManager->create('Magento\Catalog\Model\Product')->load($productid)->getWebsiteIds());
                }
                $linkModel->setPrice(isset($linksdata[$key]['price']) ? $linksdata[$key]['price'] : 0);
                $linkModel->setSortOrder(isset($linksdata[$key]['sort_order']) ? $linksdata[$key]['sort_order'] : 0);
                $linkModel->setTitle(isset($linksdata[$key]['title']) ? $linksdata[$key]['title'] : '');
                if (isset($linksdata[$key]['is_unlimited'])) {
                    if ($linksdata[$key]['is_unlimited'] == 1) {
                        $linkModel->setNumberOfDownloads(0);
                    }
                } else {
                    $linkModel->setNumberOfDownloads(isset($linksdata[$key]['number_of_downloads']) ? $linksdata[$key]['number_of_downloads'] : 0);
                }

                // setting link file
                if (isset($linksdata[$key]['type']) && $linksdata[$key]['type'] == 'file' && isset($links[$key])) {
                    $linkModel->setLinkFile("/" . $links[$key]);
                    $linkModel->setLinkType("file");
                } else if (isset($linksdata[$key]['type']) && ($linksdata[$key]['type'] == 'url') && isset($linksdata[$key]['link_url'])) {
                    $linkModel->setLinkType("url");
                    $linkModel->setLinkUrl($linksdata[$key]['link_url']);
                }

                // setting link sample file
                if (isset($linksdata[$key]['sample']['type']) && ($linksdata[$key]['sample']['type'] == 'file') && isset($link_samples[$key])) {
                    $linkModel->setSampleFile("/" . $link_samples[$key]);
                    $linkModel->setSampleType("file");
                } else if (isset($linksdata[$key]['sample']['type']) && ($linksdata[$key]['sample']['type'] == 'url') && isset($linksdata[$key] ['sample']['sample_url'])) {
                    $linkModel->setSampleType("url");
                    $linkModel->setSampleUrl($linksdata[$key]['sample']['sample_url']);
                }
                $linkModel->save();
            }
        }
        $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

    }
}
