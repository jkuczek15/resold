<?php
namespace Ced\CsMessaging\Controller\Customer\Submit;

/**
 * Interceptor class for @see \Ced\CsMessaging\Controller\Customer\Submit
 */
class Interceptor extends \Ced\CsMessaging\Controller\Customer\Submit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Escaper $escaper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, \Ced\CsMarketplace\Model\VendorFactory $vendorFactory, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Module\Manager $moduleManager, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $escaper, $scopeConfig, $messagingFactory, $vendorFactory, $transportBuilder, $inlineTranslation, $date, $moduleManager, $storeManager, $resultJsonFactory);
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
