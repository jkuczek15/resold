<?php
namespace Experius\EmailCatcher\Mail\Transport;

/**
 * Interceptor class for @see \Experius\EmailCatcher\Mail\Transport
 */
class Interceptor extends \Experius\EmailCatcher\Mail\Transport implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Zend_Mail_Transport_Sendmail $transport, \Magento\Framework\Mail\MessageInterface $message, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($transport, $message, $scopeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function sendMessage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sendMessage');
        if (!$pluginInfo) {
            return parent::sendMessage();
        } else {
            return $this->___callPlugins('sendMessage', func_get_args(), $pluginInfo);
        }
    }
}
