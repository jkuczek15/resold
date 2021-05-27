<?php
namespace Magento\Captcha\Model\DefaultModel;

/**
 * Interceptor class for @see \Magento\Captcha\Model\DefaultModel
 */
class Interceptor extends \Magento\Captcha\Model\DefaultModel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Session\SessionManagerInterface $session, \Magento\Captcha\Helper\Data $captchaData, \Magento\Captcha\Model\ResourceModel\LogFactory $resLogFactory, $formId)
    {
        $this->___init();
        parent::__construct($session, $captchaData, $resLogFactory, $formId);
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'generate');
        if (!$pluginInfo) {
            return parent::generate();
        } else {
            return $this->___callPlugins('generate', func_get_args(), $pluginInfo);
        }
    }
}
