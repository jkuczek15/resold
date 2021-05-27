<?php
namespace Magento\Framework\Webapi\Rest\Response;

/**
 * Interceptor class for @see \Magento\Framework\Webapi\Rest\Response
 */
class Interceptor extends \Magento\Framework\Webapi\Rest\Response implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Webapi\Rest\Response\RendererFactory $rendererFactory, \Magento\Framework\Webapi\ErrorProcessor $errorProcessor, \Magento\Framework\App\State $appState)
    {
        $this->___init();
        parent::__construct($rendererFactory, $errorProcessor, $appState);
    }

    /**
     * {@inheritdoc}
     */
    public function sendResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sendResponse');
        if (!$pluginInfo) {
            return parent::sendResponse();
        } else {
            return $this->___callPlugins('sendResponse', func_get_args(), $pluginInfo);
        }
    }
}
