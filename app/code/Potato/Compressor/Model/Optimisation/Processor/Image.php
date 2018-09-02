<?php
namespace Potato\Compressor\Model\Optimisation\Processor;

use Potato\Compressor\Model\Optimisation\Processor\Finder\DOM\Image as DomImage;
use Potato\Compressor\Model\Optimisation\Processor\Finder\RegExp\Css as RegExpCss;
use Potato\Compressor\Model\Optimisation\Processor\Merger\Image as MergerImage;
use Potato\Compressor\Model\Optimisation\Processor\Finder\Result\Raw;
use Potato\Compressor\Model\Optimisation\Processor\Finder\Result\Tag;
use Potato\Compressor\Helper\Css as CssHelper;
use Potato\Compressor\Helper\File as FileHelper;
use Potato\Compressor\Helper\Image as ImageHelper;
use Potato\Compressor\Helper\Data as DataHelper;
use Potato\Compressor\Helper\HtmlParser;
use Magento\Framework\App\Cache\Frontend\Factory as CacheFactory;

class Image
{
    const CSS_IMAGE_MERGE_CACHE_KEY = 'POTATO_IMAGE_MERGE_CSS_FILE';

    /** @var null|DomImage  */
    protected $tagFinder = null;

    /** @var null|RegExpCss  */
    protected $cssTagFinder = null;

    /** @var null|MergerImage  */
    protected $merger = null;
    
    /** @var CssHelper  */
    protected $cssHelper;

    /** @var FileHelper  */
    protected $fileHelper;

    /** @var ImageHelper  */
    protected $imageHelper;

    /** @var CacheFactory */
    protected $cacheFactory;

    /** @var \Zend_Cache_Backend_ExtendedInterface */
    protected $cacheInstance;

    /**
     * @param DomImage $domImage
     * @param RegExpCss $regexpCss
     * @param MergerImage $mergerImage
     * @param CssHelper $cssHelper
     * @param FileHelper $fileHelper
     * @param ImageHelper $imageHelper
     * @param CacheFactory $cacheFactory
     */
    public function __construct(
        DomImage $domImage,
        RegExpCss $regexpCss,
        MergerImage $mergerImage,
        CssHelper $cssHelper,
        FileHelper $fileHelper,
        ImageHelper $imageHelper,
        CacheFactory $cacheFactory
    ) {
        $this->tagFinder = $domImage;
        $this->cssTagFinder = $regexpCss;
        $this->merger = $mergerImage;
        $this->cssHelper = $cssHelper;
        $this->fileHelper = $fileHelper;
        $this->imageHelper = $imageHelper;
        $this->cacheFactory = $cacheFactory;

        $this->cacheInstance = $this->cacheFactory->create([])->getBackend();
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function processLazyLoad(&$html)
    {
        $replaceData = [];
        $tagList = $this->tagFinder->findAll($html);
        foreach ($tagList as $tag) {
            /** @var Tag $tag */
            $attributes = $tag->getAttributes();
            if (!array_key_exists('src', $attributes)) {
                continue;
            }
            if (strpos(trim($attributes['src']), 'data:image') === 0) {//if inline image
                continue;
            }
            if (array_key_exists('data-po-cmp-ignore', $attributes)) {
                continue;
            }
            $replaceData[] = [
                'start'   => $tag->getStart(),
                'end'     => $tag->getEnd(),
                'content' => $tag->getContentWithUpdatedAttribute(
                    [
                        'data-po-cmp-src' => $attributes['src'],
                        'src'             => DataHelper::IMAGE_PLACEHOLDER
                    ]
                )
            ];
        }
        uasort($replaceData, [$this, 'sortByStartPos']);
        $replaceData = array_values($replaceData);
        foreach (array_reverse($replaceData) as $replaceElData) {
            $html = HtmlParser::replaceIntoHtml(
                $html, $replaceElData['content'], $replaceElData['start'], $replaceElData['end']
            );
        }
        return $this;
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function processMerge(&$html)
    {
        $replaceData = [];
        $tagList = $this->tagFinder->findAll($html);
        foreach ($tagList as $key => $tag) {
            /** @var Tag $tag */
            $attributes = $tag->getAttributes();
            if (!array_key_exists('src', $attributes)) {
                unset($tagList[$key]);
                continue;
            }
            if (strpos(trim($attributes['src']), 'data:image') === 0) {//if inline image
                unset($tagList[$key]);
                continue;
            }
            if (array_key_exists('data-po-cmp-ignore', $attributes)) {
                unset($tagList[$key]);
                continue;
            }
            if (!$this->fileHelper->isInternalUrl($attributes['src'])) {
                unset($tagList[$key]);
                continue;
            }
            if (!$this->imageHelper->isImageUrl($attributes['src'])) {
                unset($tagList[$key]);
                continue;
            }
            $replaceData[] = [
                'start'   => $tag->getStart(),
                'end'     => $tag->getEnd(),
                'content' => $tag->getContentWithUpdatedAttribute(
                    [
                        'src' => DataHelper::IMAGE_PLACEHOLDER,
                        'data-po-cmp-image-id' => $attributes['src']
                    ]
                )
            ];
        }

        $mergeUrlList = $this->merger->merge($tagList);
        foreach ($mergeUrlList as $mergeUrl) {
            $mergeString = '<script type="text/javascript" src="' . $mergeUrl . '"></script>';
            $html = HtmlParser::insertStringBeforeBodyEnd($mergeString, $html);
        }

        $replaceData = array_values($replaceData);
        foreach (array_reverse($replaceData) as $replaceElData) {
            $html = HtmlParser::replaceIntoHtml(
                $html, $replaceElData['content'], $replaceElData['start'], $replaceElData['end']
            );
        }
        return $this;
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function processCSSImageMerge(&$html)
    {
        $replaceData = [];
        $tagList = $this->cssTagFinder->findAll($html);
        foreach ($tagList as $tag) {
            /** @var Tag $tag */
            $attributes = $tag->getAttributes();
            if (array_key_exists('href', $attributes)) {
                $cacheKey = self::CSS_IMAGE_MERGE_CACHE_KEY . '+' . md5($attributes['href']);
                if ($this->cacheInstance->load($cacheKey)) {
                    continue;
                }
                if (!$this->fileHelper->isLocalStaticUrl($attributes['href'])) {
                    continue;
                }
                $filePath = $this->fileHelper->getLocalPathFromUrl($attributes['href']);
                $content = $this->fileHelper->getFileContentByUrl($attributes['href']);
                $content = $this->cssHelper->inlineImagesByContent($content);
                $this->fileHelper->putContentInFile($content, $filePath);
                $this->cacheInstance->save('1', $cacheKey, array(DataHelper::COMPRESSOR_CACHE_TAG));
                continue;
            } else {
                $content = $this->cssHelper->inlineImagesByContent($tag->getContent());
            }
            $replaceData[] = [
                'start'   => $tag->getStart(),
                'end'     => $tag->getEnd(),
                'content' => $content
            ];
        }
        $replaceData = array_values($replaceData);
        foreach (array_reverse($replaceData) as $replaceElData) {
            $html = HtmlParser::replaceIntoHtml(
                $html, $replaceElData['content'], $replaceElData['start'], $replaceElData['end']
            );
        }
        return $this;
    }

    /**
     * @param Raw $a
     * @param Raw $b
     *
     * @return int
     */
    private function sortByStartPos($a, $b)
    {
        return $a['start'] - $b['start'];
    }
}
