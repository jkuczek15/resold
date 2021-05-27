<?php
namespace Magento\Backend\Block\Widget\Grid\Column\Filter\Date;

/**
 * Interceptor class for @see \Magento\Backend\Block\Widget\Grid\Column\Filter\Date
 */
class Interceptor extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Date implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\DB\Helper $resourceHelper, \Magento\Framework\Math\Random $mathRandom, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $resourceHelper, $mathRandom, $localeResolver, $dateTimeFormatter, $data);
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
