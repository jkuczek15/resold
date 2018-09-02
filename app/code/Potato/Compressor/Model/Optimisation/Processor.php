<?php
namespace Potato\Compressor\Model\Optimisation;

use Potato\Compressor\Model\Config;
use Potato\Compressor\Model\Optimisation\Processor\Css;
use Potato\Compressor\Model\Optimisation\Processor\Js;
use Potato\Compressor\Model\Optimisation\Processor\Html;
use Potato\Compressor\Model\Optimisation\Processor\Image;
use Magento\Framework\Profiler;
use Potato\Compressor\Helper\HtmlParser;

class Processor
{
    /** @var null|Css  */
    protected $cssProcessor = null;

    /** @var null|Js  */
    protected $jsProcessor = null;

    /** @var null|Image  */
    protected $imageProcessor = null;

    /** @var null|Html  */
    protected $htmlProcessor = null;

    /** @var Config  */
    protected $config;

    private $iniOriginalPcreBacktrackLimit = null;

    /**
     * Processor constructor.
     * @param Css $cssProcessor
     * @param Js $jsProcessor,
     * @param Html $htmlProcessor
     * @param Image $imageProcessor
     * @param Config $config
     */
    public function __construct(
        Css $cssProcessor,
        Js $jsProcessor,
        Html $htmlProcessor,
        Image $imageProcessor,
        Config $config
    ){
        $this->cssProcessor = $cssProcessor;
        $this->jsProcessor = $jsProcessor;
        $this->imageProcessor = $imageProcessor;
        $this->htmlProcessor = $htmlProcessor;
        $this->config = $config;
    }

    /**
     * @param \Magento\Framework\App\Response\Http $response
     */
    public function processHtmlResponse($response)
    {
        $resultHtml = $response->getBody();
        if (!$this->isCanProcessHtml($resultHtml)) {
            return;
        }

        $this->beforeProcess();
        if ($this->config->isCssDeferEnabled()) {
            Profiler::start('po-compressor-css-move');
            $this->cssProcessor->moveToBodyEnd($resultHtml);
            Profiler::stop('po-compressor-css-move');
        }
        if ($this->config->isJsDeferEnabled()) {
            Profiler::start('po-compressor-js-move');
            $this->jsProcessor->moveToBodyEnd($resultHtml);
            Profiler::stop('po-compressor-js-move');
        }

        Profiler::start('po-compressor-html-remove');
        $this->htmlProcessor->removeDuplicateIfTags($resultHtml);
        Profiler::stop('po-compressor-html-remove');

        if ($this->config->isJsMergeEnabled()) {
            Profiler::start('po-compressor-js-merge');
            $this->jsProcessor->merge($resultHtml);
            Profiler::stop('po-compressor-js-merge');
        }
        if ($this->config->isCssMergeEnabled()) {
            Profiler::start('po-compressor-css-merge');
            $this->cssProcessor->merge($resultHtml);
            Profiler::stop('po-compressor-css-merge');
        }
        if ($this->config->isJsCompressionEnabled()) {
            Profiler::start('po-compressor-js-compress');
            $this->jsProcessor->compress($resultHtml);
            Profiler::stop('po-compressor-js-compress');
        }
        if ($this->config->isCssCompressionEnabled()) {
            Profiler::start('po-compressor-css-compress');
            $this->cssProcessor->compress($resultHtml);
            Profiler::stop('po-compressor-css-compress');
        }
        if ($this->config->isJsInlineEnabled()) {
            Profiler::start('po-compressor-js-inline');
            $this->jsProcessor->inline($resultHtml);
            Profiler::stop('po-compressor-js-inline');
        }
        if ($this->config->isCssInlineEnabled()) {
            Profiler::start('po-compressor-css-inline');
            $this->cssProcessor->inline($resultHtml);
            Profiler::stop('po-compressor-css-inline');
        }

        Profiler::start('po-compressor-html-remove');
        $this->htmlProcessor->removeIgnoreFlag($resultHtml);
        $this->htmlProcessor->removeEmptyIfDirective($resultHtml);
        Profiler::stop('po-compressor-html-remove');

        if ($this->config->isImageMergeEnabled()) {
            Profiler::start('po-compressor-image-merge');
            $this->imageProcessor->processMerge($resultHtml);
            Profiler::stop('po-compressor-image-merge');
        }
        if ($this->config->isImageCSSMergeEnabled()) {
            Profiler::start('po-compressor-image-merge-in-css-files');
            $this->imageProcessor->processCSSImageMerge($resultHtml);
            Profiler::stop('po-compressor-image-merge-in-css-files');
        }
        if ($this->config->isImageLazyLoadEnabled()) {
            Profiler::start('po-compressor-image-lazyLoad');
            $this->imageProcessor->processLazyLoad($resultHtml);
            Profiler::stop('po-compressor-image-lazyLoad');
        }
        if ($this->config->isHtmlCompressionEnabled()) {
            Profiler::start('po-compressor-html-compress');
            $this->htmlProcessor->compress($resultHtml);
            Profiler::stop('po-compressor-html-compress');
        }
        $this->afterProcess();
        $response->setBody($resultHtml);
    }

    /**
     * @param string $html
     *
     * @return bool
     */
    protected function isCanProcessHtml($html)
    {
        if (!$this->config->isEnabled()) {
            return false;
        }
        if (!HtmlParser::isHtml($html)) {
            return false;
        }
        return true;
    }

    /**
     * @return $this
     */
    protected function beforeProcess()
    {
        $this->iniOriginalPcreBacktrackLimit = ini_get('pcre.backtrack_limit');
        ini_set('pcre.backtrack_limit', '10000000');
        return $this;
    }

    /**
     * @return $this
     */
    protected function afterProcess()
    {
        ini_set('pcre.backtrack_limit', $this->iniOriginalPcreBacktrackLimit);
        return $this;
    }
}
