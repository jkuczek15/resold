<?php
namespace Potato\Compressor\App\ImageMerge;

/**
 * Interceptor class for @see \Potato\Compressor\App\ImageMerge
 */
class Interceptor extends \Potato\Compressor\App\ImageMerge implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Response\Http $response, \Magento\Framework\App\Request\Http $request, \Magento\Framework\App\Cache\Frontend\Factory $cacheFactory)
    {
        $this->___init();
        parent::__construct($response, $request, $cacheFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function launch()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'launch');
        if (!$pluginInfo) {
            return parent::launch();
        } else {
            return $this->___callPlugins('launch', func_get_args(), $pluginInfo);
        }
    }
}
