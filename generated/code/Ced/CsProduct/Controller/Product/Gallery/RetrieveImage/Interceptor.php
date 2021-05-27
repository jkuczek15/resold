<?php
namespace Ced\CsProduct\Controller\Product\Gallery\RetrieveImage;

/**
 * Interceptor class for @see \Ced\CsProduct\Controller\Product\Gallery\RetrieveImage
 */
class Interceptor extends \Ced\CsProduct\Controller\Product\Gallery\RetrieveImage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Catalog\Model\Product\Media\Config $mediaConfig, \Magento\Framework\Filesystem $fileSystem, \Magento\Framework\Image\AdapterFactory $imageAdapterFactory, \Magento\Framework\HTTP\Adapter\Curl $curl, \Magento\MediaStorage\Model\ResourceModel\File\Storage\File $fileUtility)
    {
        $this->___init();
        parent::__construct($context, $resultRawFactory, $mediaConfig, $fileSystem, $imageAdapterFactory, $curl, $fileUtility);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
