<?php
namespace Magezon\Builder\Controller\Adminhtml\Ajax\Template;

/**
 * Interceptor class for @see \Magezon\Builder\Controller\Adminhtml\Ajax\Template
 */
class Interceptor extends \Magezon\Builder\Controller\Adminhtml\Ajax\Template implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\HTTP\ClientInterface $client, \Magento\Framework\Filesystem\Io\File $file, \Magezon\Core\Helper\Data $coreHelper)
    {
        $this->___init();
        parent::__construct($context, $filesystem, $client, $file, $coreHelper);
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
