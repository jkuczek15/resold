<?php
namespace Magento\Review\Block\Adminhtml\Main;

/**
 * Interceptor class for @see \Magento\Review\Block\Adminhtml\Main
 */
class Interceptor extends \Magento\Review\Block\Adminhtml\Main implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Framework\Registry $registry, \Magento\Customer\Helper\View $customerViewHelper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $customerRepository, $productFactory, $registry, $customerViewHelper, $data);
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
