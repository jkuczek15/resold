<?php
namespace Potato\Compressor\Model\Optimisation\Processor\Minifier;

use Potato\Compressor\Model\Optimisation\Processor\Finder\Result\Tag;
use Potato\Compressor\Helper\File as FileHelper;
use Potato\Compressor\Helper\Data as DataHelper;

abstract class AbstractMinifier
{

    protected $fileHelper;
    
    protected $dataHelper;
    
    public function __construct(FileHelper $fileHelper, DataHelper $dataHelper)
    {
        $this->fileHelper = $fileHelper;
        $this->dataHelper = $dataHelper;
    }
    
    /**
     * @param Tag $tag
     *
     * @return string|null
     */
    public function minify($tag)
    {
        $urlToFile = $this->getPathFromTag($tag);
        if (!$this->fileHelper->isInternalUrl($urlToFile)) {
            return $urlToFile;
        }
        $file = realpath(
            $this->getLocalPath($urlToFile)
        );
        $targetFilename = $this->getTargetFilename($file);
        $targetFile = $this->dataHelper->getRootCachePath() . DIRECTORY_SEPARATOR . $targetFilename;
        if (file_exists($targetFile)) {
            $timeOfCurrentFile = filemtime($file);
            $timeOfNewFile = filemtime($targetFile);
            if (FALSE !== $timeOfCurrentFile && FALSE !== $timeOfNewFile
                && $timeOfCurrentFile < $timeOfNewFile
            ) {
                return $this->dataHelper->getRootCacheUrl() . '/' . $targetFilename;
            }
        }
        $content = file_get_contents($file);
        $content = $this->beforeMinifyFile($file, $content);
        $resultContent = $this->minifyContent($content);
        if (strlen(trim($resultContent)) === 0) {
            return null;
        }
        $this->fileHelper->putContentInFile($resultContent, $targetFile);
        return $this->dataHelper->getRootCacheUrl() . '/' . $targetFilename;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    abstract public function minifyContent($content);

    /**
     * @param string $file
     *
     * @return string
     */
    abstract protected function getTargetFilename($file);

    /**
     * @param Tag $tag
     *
     * @return string
     */
    abstract protected function getPathFromTag($tag);

    /**
     * @param string $url
     *
     * @return string
     */
    protected function getLocalPath($url)
    {
        return $this->fileHelper->getLocalPathFromUrl($url);
    }

    /**
     * @param string $file
     * @param string $content
     *
     * @return string
     */
    protected function beforeMinifyFile($file, $content)
    {
        return $content;
    }
}
