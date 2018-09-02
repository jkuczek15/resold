<?php
namespace Potato\Compressor\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Potato\Compressor\Helper\Data as DataHelper;
use Potato\Compressor\Model\Config;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class File
 */
class File
{
    const FILE_CONTENT_LENGTH_CACHE_KEY = "POTATO_COMPRESSOR_FILE_CONTENT_LENGTH";

    /** @var Filesystem  */
    protected $filesystem;

    /** @var Config  */
    protected $config;

    /** @var CacheInterface  */
    protected $cache;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /**
     * File constructor.
     * @param Filesystem $filesystem
     * @param Config $config
     * @param CacheInterface $cache
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        Config $config,
        CacheInterface $cache,
        StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->config = $config;
        $this->cache = $cache;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getLocalPathFromUrl($url)
    {
        $baseUrlList = [
            [
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
                $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath()
            ],
            [
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA, true),
                $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath()
            ],
            [
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_STATIC),
                $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW)->getAbsolutePath()
            ],
            [
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_STATIC, true),
                $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW)->getAbsolutePath()
            ],
            [
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB),
                $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath()
            ],
            [
                $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB, true),
                $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath()
            ],
        ];
        
        foreach ($baseUrlList as $baseUrlData) {
            if (strpos($url, $baseUrlData[0]) === 0) {
                $url = str_replace($baseUrlData[0], $baseUrlData[1], $url);
                continue;
            }
        }
        if ($fragment = parse_url($url, PHP_URL_FRAGMENT)) {
            $url = str_replace('#' . $fragment, '', $url);
        }
        if ($query = parse_url($url, PHP_URL_QUERY)) {
            $url = str_replace('?' . $query, '', $url);
        }
        $url = rtrim($url, '?#');
        return $url;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isInternalUrl($url)
    {
        $isUrl = false;
        $isUrl = $isUrl || strpos($url, 'http://') === 0;
        $isUrl = $isUrl || strpos($url, 'https://') === 0;
        $isUrl = $isUrl || strpos($url, '//') === 0;
        if (!$isUrl) {
            return false;
        }
        return
            self::isLocalWebUrl($url) ||
            self::isLocalMediaUrl($url) ||
            self::isLocalStaticUrl($url)
        ;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isLocalWebUrl($url)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        $secureBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB, true);
        
        return strpos($url, $baseUrl) === 0 || strpos($url, $secureBaseUrl) === 0;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isLocalMediaUrl($url)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $secureBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA, true);
        return strpos($url, $baseUrl) === 0 || strpos($url, $secureBaseUrl) === 0;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isLocalStaticUrl($url)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_STATIC);
        $secureBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_STATIC, true);
        return strpos($url, $baseUrl) === 0 || strpos($url, $secureBaseUrl) === 0;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getFileContentByUrl($url)
    {
        $localPath = $this->getLocalPathFromUrl($url);
        return file_get_contents($localPath);
    }

    /**
     * @param string $url
     *
     * @return int
     */
    public function getStringLengthFromUrl($url)
    {
        $resultFromCache = $this->cache->load(self::FILE_CONTENT_LENGTH_CACHE_KEY . '_' . $url);
        if ($resultFromCache > 0) {
            return $resultFromCache;
        }
        $content = $this->getFileContentByUrl($url);
        $result = strlen($content);
        $this->cache->save(
            $result, self::FILE_CONTENT_LENGTH_CACHE_KEY . '_' . $url,
            [DataHelper::COMPRESSOR_CACHE_TAG]
        );
        return strlen($content);
    }

    /**
     * @param string $content
     * @param string $dir
     * @param string $extension
     *
     * @return string
     */
    public function createFileByContent($content, $dir, $extension)
    {
        $filename = md5($content) . '-' . strlen($content) . '.' . $extension;
        $path = $dir . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($path)) {
            return $path;
        }
        $this->putContentInFile($content, $path);
        return $path;
    }

    /**
     * @param string $content
     * @param string $filePath
     *
     * @throws \Exception
     */
    public function putContentInFile($content, $filePath)
    {
        $path = str_replace(BP . DIRECTORY_SEPARATOR, '', $filePath);
        $pathToTarget = BP;
        $pathMap = explode(DIRECTORY_SEPARATOR, $path);
        foreach ($pathMap as $key => $pathPart) {
            $pathToTarget .= DIRECTORY_SEPARATOR . $pathPart;
            if (file_exists($pathToTarget) && is_dir($pathToTarget)) {
                continue;
            }
            if ($key === (count($pathMap) - 1)) {//last element of array
                $result = file_put_contents($pathToTarget, $content, LOCK_EX);
                if (FALSE === $result) {
                    throw new \Exception('Unable to put content in file: ' . $pathToTarget);
                }
                @chmod($pathToTarget, $this->config->getFilePermission());
                break;
            }
            if (!@mkdir($pathToTarget, $this->config->getFolderPermission(), false)) {
                throw new \Exception('Unable to create directory: ' . $pathToTarget);
            }
        }
    }

    /**
     * @param string $dirPath
     * @param string[] $excludeList
     */
    public function removeDirectory($dirPath, $excludeList = [])
    {
        foreach (scandir($dirPath) as $item) {
            if (!strcmp($item, '.') || !strcmp($item, '..')) {
                continue;
            }
            $path = $dirPath . DIRECTORY_SEPARATOR . $item;
            if (FALSE !== array_search($path, $excludeList)) {
                continue;
            }
            if (is_dir($path)) {
                \Magento\Framework\Filesystem\Io\File::rmdirRecursive($path);
            } else {
                @unlink($path);
            }
        }
    }

    /**
     * @param string $type
     * @return string
     */
    public function getAbsolutePath($type)
    {
        return $this->filesystem->getDirectoryRead($type)->getAbsolutePath();
    }

    /**
     * @param string[] $urlList
     *
     * @return int
     */
    public function getLastFileChangeTimestampForUrlList($urlList)
    {
        $timestampList = [];
        foreach ($urlList as $url) {
            $filePath = $this->getLocalPathFromUrl($url);
            $timestampList[] = @filemtime($filePath);
        }
        return max($timestampList);
    }

    /**
     * @param string $dirPath
     * @param string $regExp
     *
     * @return \RegexIterator
     */
    public function recursiveSearch($dirPath, $regExp)
    {
        $dirIterator = new \RecursiveDirectoryIterator($dirPath);
        $iterator = new \RecursiveIteratorIterator($dirIterator);
        $regex = new \RegexIterator($iterator, $regExp, \RecursiveRegexIterator::GET_MATCH);
        return $regex;
    }
}
