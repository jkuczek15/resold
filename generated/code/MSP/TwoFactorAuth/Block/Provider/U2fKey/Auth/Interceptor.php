<?php
namespace MSP\TwoFactorAuth\Block\Provider\U2fKey\Auth;

/**
 * Interceptor class for @see \MSP\TwoFactorAuth\Block\Provider\U2fKey\Auth
 */
class Interceptor extends \MSP\TwoFactorAuth\Block\Provider\U2fKey\Auth implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Model\Auth\Session $session, \MSP\TwoFactorAuth\Model\Provider\Engine\U2fKey $u2fKey, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $session, $u2fKey, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}
