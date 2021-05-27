<?php
namespace Magento\Paypal\Block\Billing\Agreements;

/**
 * Interceptor class for @see \Magento\Paypal\Block\Billing\Agreements
 */
class Interceptor extends \Magento\Paypal\Block\Billing\Agreements implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Paypal\Model\ResourceModel\Billing\Agreement\CollectionFactory $agreementCollection, \Magento\Paypal\Helper\Data $helper, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $customerSession, $agreementCollection, $helper, $data);
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
