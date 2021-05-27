<?php
namespace Resold\Api\Controller\Vendor\Update;

/**
 * Interceptor class for @see \Resold\Api\Controller\Vendor\Update
 */
class Interceptor extends \Resold\Api\Controller\Vendor\Update implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Ced\CsMarketplace\Model\Vendor $vendor, \Thai\S3\Model\MediaStorage\File\Storage\S3 $storage, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
    {
        $this->___init();
        parent::__construct($context, $vendor, $storage, $resultJsonFactory);
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
