<?php
namespace Magento\Backend\Block\Menu;

/**
 * Interceptor class for @see \Magento\Backend\Block\Menu
 */
class Interceptor extends \Magento\Backend\Block\Menu implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Model\UrlInterface $url, \Magento\Backend\Model\Menu\Filter\IteratorFactory $iteratorFactory, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Backend\Model\Menu\Config $menuConfig, \Magento\Framework\Locale\ResolverInterface $localeResolver, array $data = [], \Magento\Backend\Block\MenuItemChecker $menuItemChecker = null, \Magento\Backend\Block\AnchorRenderer $anchorRenderer = null)
    {
        $this->___init();
        parent::__construct($context, $url, $iteratorFactory, $authSession, $menuConfig, $localeResolver, $data, $menuItemChecker, $anchorRenderer);
    }

    /**
     * {@inheritdoc}
     */
    public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'renderNavigation');
        if (!$pluginInfo) {
            return parent::renderNavigation($menu, $level, $limit, $colBrakes);
        } else {
            return $this->___callPlugins('renderNavigation', func_get_args(), $pluginInfo);
        }
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
