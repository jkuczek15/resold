<?php
namespace Mageplaza\Seo\Block\Adminhtml\SeoChecker\CheckForm;

/**
 * Interceptor class for @see \Mageplaza\Seo\Block\Adminhtml\SeoChecker\CheckForm
 */
class Interceptor extends \Mageplaza\Seo\Block\Adminhtml\SeoChecker\CheckForm implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Url $url, \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder $cmsUrl, \Magento\Cms\Model\PageFactory $cmsPageFactory, \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $sitemapCollection, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository, \Mageplaza\Seo\Helper\Data $helper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $url, $cmsUrl, $cmsPageFactory, $sitemapCollection, $jsonHelper, $productFactory, $productRepository, $categoryRepository, $helper, $data);
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
