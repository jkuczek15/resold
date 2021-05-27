<?php
namespace Mageplaza\Core\Controller\Adminhtml\Index\Activate;

/**
 * Interceptor class for @see \Mageplaza\Core\Controller\Adminhtml\Index\Activate
 */
class Interceptor extends \Mageplaza\Core\Controller\Adminhtml\Index\Activate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Config\Model\ResourceModel\Config $resourceConfig, \Magento\Framework\App\Config\ReinitableConfigInterface $config, \Mageplaza\Core\Helper\Validate $helper, \Mageplaza\Core\Model\ActivateFactory $activateFactory)
    {
        $this->___init();
        parent::__construct($context, $resourceConfig, $config, $helper, $activateFactory);
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
