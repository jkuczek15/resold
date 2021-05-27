<?php
namespace Magento\TaxImportExport\Block\Adminhtml\Rate\Grid\Renderer\Country;

/**
 * Interceptor class for @see \Magento\TaxImportExport\Block\Adminhtml\Rate\Grid\Renderer\Country
 */
class Interceptor extends \Magento\TaxImportExport\Block\Adminhtml\Rate\Grid\Renderer\Country implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Context $context, \Magento\Framework\Locale\ListsInterface $localeLists, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $localeLists, $data);
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
