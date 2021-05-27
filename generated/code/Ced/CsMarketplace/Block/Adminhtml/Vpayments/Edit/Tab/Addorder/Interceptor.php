<?php
namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit\Tab\Addorder;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit\Tab\Addorder
 */
class Interceptor extends \Ced\CsMarketplace\Block\Adminhtml\Vpayments\Edit\Tab\Addorder implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, \Ced\CsMarketplace\Model\Vendor $vendor, \Ced\CsMarketplace\Model\Vorders $vorders, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, \Magento\Backend\Model\Url $urlBuilder, \Magento\Framework\Data\FormFactory $formFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $objectManager, $vendor, $vorders, $localeCurrency, $urlBuilder, $formFactory, $data);
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
