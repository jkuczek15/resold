<?php
namespace Mirasvit\Core\Model\Mail\Template\TransportBuilder;

/**
 * Interceptor class for @see \Mirasvit\Core\Model\Mail\Template\TransportBuilder
 */
class Interceptor extends \Mirasvit\Core\Model\Mail\Template\TransportBuilder implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\Core\Helper\Module $moduleHelper, \Magento\Framework\Mail\Template\FactoryInterface $templateFactory, \Magento\Framework\Mail\MessageInterface $message, \Magento\Framework\Mail\Template\SenderResolverInterface $senderResolver, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\Mail\TransportInterfaceFactory $mailTransportFactory)
    {
        $this->___init();
        parent::__construct($moduleHelper, $templateFactory, $message, $senderResolver, $objectManager, $mailTransportFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateOptions($templateOptions)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateOptions');
        if (!$pluginInfo) {
            return parent::setTemplateOptions($templateOptions);
        } else {
            return $this->___callPlugins('setTemplateOptions', func_get_args(), $pluginInfo);
        }
    }
}
