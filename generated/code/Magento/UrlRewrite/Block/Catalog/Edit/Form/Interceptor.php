<?php
namespace Magento\UrlRewrite\Block\Catalog\Edit\Form;

/**
 * Interceptor class for @see \Magento\UrlRewrite\Block\Catalog\Edit\Form
 */
class Interceptor extends \Magento\UrlRewrite\Block\Catalog\Edit\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\UrlRewrite\Model\OptionProvider $optionProvider, \Magento\UrlRewrite\Model\UrlRewriteFactory $rewriteFactory, \Magento\Store\Model\System\Store $systemStore, \Magento\Backend\Helper\Data $adminhtmlData, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator, \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $optionProvider, $rewriteFactory, $systemStore, $adminhtmlData, $productFactory, $categoryFactory, $productUrlPathGenerator, $categoryUrlPathGenerator, $data);
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
