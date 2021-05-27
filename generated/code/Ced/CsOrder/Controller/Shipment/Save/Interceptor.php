<?php
namespace Ced\CsOrder\Controller\Shipment\Save;

/**
 * Interceptor class for @see \Ced\CsOrder\Controller\Shipment\Save
 */
class Interceptor extends \Ced\CsOrder\Controller\Shipment\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Module\Manager $moduleManager, \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader, \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator, \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager, $shipmentLoader, $labelGenerator, $shipmentSender, $registry);
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
