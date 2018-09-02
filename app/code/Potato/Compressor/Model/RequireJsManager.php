<?php
namespace Potato\Compressor\Model;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\Cache\Frontend\Factory as CacheFactory;
use Potato\Compressor\Helper\Data as DataHelper;
use Potato\Compressor\Helper\File as FileHelper;
use Magento\Framework\View\Element\Template\Context;
use Potato\Compressor\Model\Optimisation\Processor\Minifier\Js as JsMinify;

class RequireJsManager
{
    const SCRIPT_TAG_DATA_KEY = 'data-po-cmp-requirejs-key';
    const CACHE_KEY_PREFIX = 'POTATO_COMPRESSOR_REQUIREJS_';

    /** @var  Config */
    protected $config;

    /** @var  CacheFactory */
    protected $cacheFactory;

    /** @var  FileHelper */
    protected $fileHelper;

    /** @var  Context */
    protected $context;

    /** @var  JsMinify */
    protected $jsMinify;

    public function __construct(
        Config $config,
        CacheFactory $cacheFactory,
        FileHelper $fileHelper,
        Context $context,
        JsMinify $jsMinify
    ) {
        $this->config = $config;
        $this->cacheFactory = $cacheFactory;
        $this->fileHelper = $fileHelper;
        $this->context = $context;
        $this->jsMinify = $jsMinify;
    }

    /**
     * @param LayoutInterface $layout
     *
     * @return string
     */
    public function getRouteKeyByLayout(LayoutInterface $layout)
    {
        $handleList = $layout->getUpdate()->getHandles();
        $handleList = array_slice($handleList, 0, 2);
        $result = join('---', $handleList);
        $result .= '||' . md5($this->context->getDesignPackage()->getDesignTheme()->getCode());
        $result .= '||' . $this->context->getStoreManager()->getStore()->getId();
        return $result;
    }

    /**
     * @return string
     */
    public function getRouteKeyByCurrentContext()
    {
        return $this->getRouteKeyByLayout($this->context->getLayout());
    }

    /**
     * @param string $routeKey
     *
     * @return bool
     */
    public function isDataExists($routeKey)
    {
        $cacheInstance = $this->cacheFactory->create([])->getBackend();
        return !!$cacheInstance->load(self::CACHE_KEY_PREFIX . $routeKey);
    }

    /**
     * @param array $list
     * @param string $routeKey
     *
     * @return bool
     */
    public function saveUrlList($list, $routeKey)
    {
        $cacheInstance = $this->cacheFactory->create([])->getBackend();
        return $cacheInstance->save(
            \Zend_Json::encode($list),
            self::CACHE_KEY_PREFIX . $routeKey,
            [DataHelper::COMPRESSOR_CACHE_TAG],
            null
        );
    }

    /**
     * @param string $routeKey
     *
     * @return array|null
     */
    public function loadUrlList($routeKey)
    {
        if (null === $routeKey) {
            $routeKey = $this->getRouteKeyByCurrentContext();
        }
        $cacheInstance = $this->cacheFactory->create([])->getBackend();
        $data = $cacheInstance->load(
            self::CACHE_KEY_PREFIX . $routeKey
        );
        if (!is_string($data)) {
            return null;
        }
        return \Zend_Json::decode($data);
    }

    /**
     * @param string $routeKey
     *
     * @return array|null
     */
    public function loadFileList($routeKey)
    {
        $list = $this->loadUrlList($routeKey);
        if (null === $list) {
            return null;
        }
        $result = [];
        foreach ($list as $url) {
            if (!$this->fileHelper->isInternalUrl($url)) {
                continue;
            }
            $result[] = $this->fileHelper->getLocalPathFromUrl($url);
        }
        return $result;
    }

    /**
     * @param string $routeKey
     * @param string $fileExtension
     * @param null $callbackOnContent
     *
     * @return array|null
     */
    public function getInlineConfig($routeKey, $fileExtension = '.js', $callbackOnContent = null)
    {
        $urlList = $this->loadUrlList($routeKey);
        if (null === $urlList) {
            return null;
        }
        $baseUrl = $this->context->getUrlBuilder()->getBaseUrl(['_secure' => $this->context->getRequest()->isSecure()]);
        $assetRegexp = str_replace(
            $baseUrl,
            '',
            $this->context->getAssetRepository()->getUrlWithParams('/', ['_secure' => $this->context->getRequest()->isSecure()])
        );
        $assetRegexp = str_replace('/_view/', '/.*?/', $assetRegexp);
        $assetRegexp = '/^' . str_replace('/', '\/', $assetRegexp) . '/';
        $config = [];
        foreach ($urlList as $url) {
            if (!$this->fileHelper->isInternalUrl($url)) {
                continue;
            }
            $currentFileExtension = substr($url, strlen($fileExtension) * -1);
            if ($currentFileExtension !== $fileExtension) {
                continue;
            }
            $key = ltrim(str_replace($baseUrl, '', $url), '/');
            $key = preg_replace($assetRegexp, '', $key);
            $key = ltrim($key, '/');
            $content = $this->fileHelper->getFileContentByUrl($url);
            if (null !== $callbackOnContent && is_callable($callbackOnContent)) {
                $content = call_user_func($callbackOnContent, $content);
            }
            $config[$key] = $content;
        }
        if (array_key_exists(DataHelper::LIB_JS_BUILD_SCRIPT, $config)) {
            unset($config[DataHelper::LIB_JS_BUILD_SCRIPT]);
        }
        return $config;
    }

    /**
     * @param string $routeKey
     *
     * @return string
     */
    public function getRequireJsContent($routeKey)
    {
        $jsCallback = null;
        $htmlCallback = null;
        if ($this->config->isJsCompressionEnabled()) {
            $jsCallback = [
                $this->jsMinify,
                'minifyContent'
            ];
        }
        if ($this->config->isHtmlCompressionEnabled()) {
            $htmlCallback = [
                '\Potato\Compressor\Lib\Minify\HTMLMax',
                'minify'
            ];
        }
        $config = [
            'jsbuild' => $this->getInlineConfig($routeKey, '.js', $jsCallback),
            'text' => $this->getInlineConfig($routeKey, '.html', $htmlCallback)
        ];

        $content = "require.config({config:" . \Zend_Json::encode($config) . "});";
        $content .= <<<EOL
require.config({
    bundles: {
        'mage/requirejs/static': [
            'jsbuild',
            'buildTools',
            'text',
            'statistician'
        ]
    },
    deps: ['jsbuild']
});
EOL;
        return $content;
    }
}
