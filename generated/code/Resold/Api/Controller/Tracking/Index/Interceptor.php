<?php
namespace Resold\Api\Controller\Tracking\Index;

/**
 * Interceptor class for @see \Resold\Api\Controller\Tracking\Index
 */
class Interceptor extends \Resold\Api\Controller\Tracking\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultJsonFactory, $trackFactory, $transportBuilder, $formKeyValidator, $scopeConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
