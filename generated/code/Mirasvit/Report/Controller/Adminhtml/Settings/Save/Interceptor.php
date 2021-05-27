<?php
namespace Mirasvit\Report\Controller\Adminhtml\Settings\Save;

/**
 * Interceptor class for @see \Mirasvit\Report\Controller\Adminhtml\Settings\Save
 */
class Interceptor extends \Mirasvit\Report\Controller\Adminhtml\Settings\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\Report\Api\Service\ColumnManagerInterface $columnManager, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($columnManager, $context);
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
