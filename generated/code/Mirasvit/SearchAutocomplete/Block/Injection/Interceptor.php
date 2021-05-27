<?php
namespace Mirasvit\SearchAutocomplete\Block\Injection;

/**
 * Interceptor class for @see \Mirasvit\SearchAutocomplete\Block\Injection
 */
class Interceptor extends \Mirasvit\SearchAutocomplete\Block\Injection implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Mirasvit\SearchAutocomplete\Model\Config $config, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Search\Helper\Data $searchHelper)
    {
        $this->___init();
        parent::__construct($context, $config, $localeFormat, $searchHelper);
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
