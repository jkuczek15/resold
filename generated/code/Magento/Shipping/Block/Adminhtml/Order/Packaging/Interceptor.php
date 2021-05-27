<?php
namespace Magento\Shipping\Block\Adminhtml\Order\Packaging;

/**
 * Interceptor class for @see \Magento\Shipping\Block\Adminhtml\Order\Packaging
 */
class Interceptor extends \Magento\Shipping\Block\Adminhtml\Order\Packaging implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Shipping\Model\Carrier\Source\GenericInterface $sourceSizeModel, \Magento\Framework\Registry $coreRegistry, \Magento\Shipping\Model\CarrierFactory $carrierFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $sourceSizeModel, $coreRegistry, $carrierFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayGirthValue()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDisplayGirthValue');
        if (!$pluginInfo) {
            return parent::isDisplayGirthValue();
        } else {
            return $this->___callPlugins('isDisplayGirthValue', func_get_args(), $pluginInfo);
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
