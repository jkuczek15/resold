<?php
namespace Mirasvit\SearchElastic\Controller\Adminhtml\Command\Status;

/**
 * Interceptor class for @see \Mirasvit\SearchElastic\Controller\Adminhtml\Command\Status
 */
class Interceptor extends \Mirasvit\SearchElastic\Controller\Adminhtml\Command\Status implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Mirasvit\SearchElastic\Model\Engine $engine)
    {
        $this->___init();
        parent::__construct($context, $engine);
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
