<?php
namespace Potato\Compressor\Model\Optimisation\Processor\Merger;

use Potato\Compressor\Model\Config;
use Potato\Compressor\Model\Optimisation\Processor\Minifier\Js as JsMinifier;
use Potato\Compressor\Helper\Data as DataHelper;
use Potato\Compressor\Helper\Image as ImageHelper;
use Potato\Compressor\Helper\File as FileHelper;
use Potato\Compressor\Model\Optimisation\Processor\Finder\Result\Tag;
use Magento\Store\Model\StoreManagerInterface;

class Image
{
    const CACHE_KEY = "POTATO_COMPRESSOR_IMAGE_MERGE";

    /** @var JsMinifier */
    protected $minifier = null;

    /** @var DataHelper  */
    protected $dataHelper;

    /** @var ImageHelper  */
    protected $imageHelper;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var Config */
    protected $config;

    /**
     * Image constructor.
     * @param JsMinifier $jsMinifier
     * @param ImageHelper $imageHelper
     * @param DataHelper $dataHelper
     * @param FileHelper $fileHelper
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     */
    public function __construct(
        JsMinifier $jsMinifier,
        ImageHelper $imageHelper,
        DataHelper $dataHelper,
        FileHelper $fileHelper,
        StoreManagerInterface $storeManager,
        Config $config
    ) {
        $this->minifier = $jsMinifier;
        $this->imageHelper = $imageHelper;
        $this->dataHelper = $dataHelper;
        $this->fileHelper = $fileHelper;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * @param Tag[] $list
     *
     * @return string[]
     */
    public function merge($list)
    {
        $this->cleanStorage();
        $data = [];
        $sizeMap = [];
        foreach ($list as $key => $tag) {
            $attributes = $tag->getAttributes();
            $inlineImage = $this->imageHelper->getInlineImageByUrl($attributes['src']);
            if (null !== $inlineImage) {
                $data[$attributes['src']] = $inlineImage;
                $sizeMap[$attributes['src']] = strlen($inlineImage);
            }
        }

        $limit = $this->config->getImageMergeMaxFileSizeInBytes();
        if (null === $limit) {
            return array($this->mergeGroup($data));
        }
        $result = array();
        $resultListKey = 0;
        $currentFileSize = 0;
        foreach (array_reverse($sizeMap) as $file => $fileSize) {
            $currentFileSize += $fileSize;
            if ($currentFileSize > $limit) {
                $currentFileSize = $fileSize;
                $resultListKey++;
            }
            if (!array_key_exists($resultListKey, $result)) {
                $result[$resultListKey] = array();
            }
            $result[$resultListKey][$file] = $data[$file];
        }
        $resultUrlList = array();
        foreach ($result as $resultData) {
            $resultUrlList[] = $this->mergeGroup($resultData);
        }
        return array_reverse($resultUrlList);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    protected function mergeGroup($data)
    {
        $result = '(function(){';
        $result .= 'var DATA=' . \Zend_Json::encode($data) . ';';
        $result .= $this->getRunScript();
        $result .= '})();';
        $fileList = array_keys($data);
        sort($fileList);
        $cacheKey = md5(implode(',', $fileList));
        $filePath = $this->dataHelper->getImageMergeCachePath()
            . DIRECTORY_SEPARATOR . $cacheKey[0]
            . DIRECTORY_SEPARATOR . $cacheKey[1]
            . DIRECTORY_SEPARATOR . $cacheKey . '.js'
        ;
        $urlPath = $this->dataHelper->getImageMergeCacheUrl($this->storeManager->getStore()->isCurrentlySecure())
            . '/' . $cacheKey[0] . '/' . $cacheKey[1]
            . '/' . $cacheKey . '.js'
        ;
        if (!file_exists($filePath)) {
            $this->fileHelper->putContentInFile($result, $filePath);
        }
        return $urlPath;
    }

    /**
     * @return $this
     */
    protected function cleanStorage()
    {
        $baseDirPath = $this->dataHelper->getImageMergeCachePath();
        $timeToRemove = 1800;//30 minutes in sec
        $timestamp = time();
        $list = $this->fileHelper->recursiveSearch($baseDirPath, '/\.js$/i');
        foreach($list as $path => $object) {
            $fileTime = @filemtime($path);
            if ($timestamp < $fileTime + $timeToRemove) {
                continue;
            }
            @unlink($path);
        }
        return $this;
    }

    /**
     * @return string
     */
    private function getRunScript()
    {
        $scriptContent = <<<EOL
document.addEventListener("DOMContentLoaded", function(event){
    var imgList = document.getElementsByTagName('img');
    for (var i=0; i < imgList.length; i++) {
        var el = imgList[i];
        var imageId = el.getAttribute('data-po-cmp-image-id');
        if (!imageId) {
            continue;
        }
        if (typeof(DATA[imageId]) === "undefined") {
            continue;
        }
        el.src = DATA[imageId];
    }
});
EOL;
        return $this->minifier->minifyContent($scriptContent);
    }
}
