<?php
namespace Ced\CsOrder\Block\Order\View\Tab\Transactions;

/**
 * Interceptor class for @see \Ced\CsOrder\Block\Order\View\Tab\Transactions
 */
class Interceptor extends \Ced\CsOrder\Block\Order\View\Tab\Transactions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Context $context, \Magento\Framework\AuthorizationInterface $authorization, \Magento\Framework\Registry $registry, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $authorization, $registry, $data);
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
