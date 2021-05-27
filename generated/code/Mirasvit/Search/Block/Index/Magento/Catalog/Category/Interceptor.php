<?php
namespace Mirasvit\Search\Block\Index\Magento\Catalog\Category;

/**
 * Interceptor class for @see \Mirasvit\Search\Block\Index\Magento\Catalog\Category
 */
class Interceptor extends \Mirasvit\Search\Block\Index\Magento\Catalog\Category implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\Catalog\Helper\Output $outputHelper, \Mirasvit\Search\Api\Service\IndexServiceInterface $indexService, \Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\View\Element\Template\Context $context)
    {
        $this->___init();
        parent::__construct($categoryFactory, $outputHelper, $indexService, $objectManager, $context);
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
