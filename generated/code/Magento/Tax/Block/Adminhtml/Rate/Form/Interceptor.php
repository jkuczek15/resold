<?php
namespace Magento\Tax\Block\Adminhtml\Rate\Form;

/**
 * Interceptor class for @see \Magento\Tax\Block\Adminhtml\Rate\Form
 */
class Interceptor extends \Magento\Tax\Block\Adminhtml\Rate\Form implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Directory\Model\RegionFactory $regionFactory, \Magento\Directory\Model\Config\Source\Country $country, \Magento\Tax\Block\Adminhtml\Rate\Title\FieldsetFactory $fieldsetFactory, \Magento\Tax\Helper\Data $taxData, \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository, \Magento\Tax\Model\TaxRateCollection $taxRateCollection, \Magento\Tax\Model\Calculation\Rate\Converter $taxRateConverter, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $formFactory, $regionFactory, $country, $fieldsetFactory, $taxData, $taxRateRepository, $taxRateCollection, $taxRateConverter, $data);
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
