<?php
namespace Magento\Framework\View\Element\Html\Calendar;

/**
 * Interceptor class for @see \Magento\Framework\View\Element\Html\Calendar
 */
class Interceptor extends \Magento\Framework\View\Element\Html\Calendar implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Json\EncoderInterface $encoder, \Magento\Framework\Locale\ResolverInterface $localeResolver, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $date, $encoder, $localeResolver, $data);
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
