<?php
namespace Ced\CsMessaging\Controller\Adminhtml\Vendor\Adminchatsubmit;

/**
 * Interceptor class for @see \Ced\CsMessaging\Controller\Adminhtml\Vendor\Adminchatsubmit
 */
class Interceptor extends \Ced\CsMessaging\Controller\Adminhtml\Vendor\Adminchatsubmit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Ced\CsMarketplace\Model\VendorFactory $vendorFactory, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $transportBuilder, $inlineTranslation, $scopeConfig, $vendorFactory, $messagingFactory, $date, $storeManager);
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
