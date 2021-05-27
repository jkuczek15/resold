<?php
namespace Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date;

/**
 * Interceptor class for @see \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date
 */
class Interceptor extends \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter, \Magento\Framework\Locale\ResolverInterface $localeResolver, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $dateTimeFormatter, $localeResolver, $data);
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
