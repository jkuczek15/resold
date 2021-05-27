<?php
namespace Mageplaza\Smtp\Controller\Adminhtml\Smtp\Test;

/**
 * Interceptor class for @see \Mageplaza\Smtp\Controller\Adminhtml\Smtp\Test
 */
class Interceptor extends \Mageplaza\Smtp\Controller\Adminhtml\Smtp\Test implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Psr\Log\LoggerInterface $logger, \Mageplaza\Smtp\Helper\Data $smtpDataHelper, \Mageplaza\Smtp\Mail\Rse\Mail $mailResource, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder)
    {
        $this->___init();
        parent::__construct($context, $logger, $smtpDataHelper, $mailResource, $transportBuilder);
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
