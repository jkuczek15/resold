<?php
namespace Ced\CsMessaging\Controller\Frontend\Savechat;

/**
 * Interceptor class for @see \Ced\CsMessaging\Controller\Frontend\Savechat
 */
class Interceptor extends \Ced\CsMessaging\Controller\Frontend\Savechat implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\UrlFactory $urlFactory, \Magento\Framework\Escaper $escaper, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Ced\CsMessaging\Model\MessagingFactory $messagingFactory, \Ced\CsMarketplace\Model\VendorFactory $vendorFactory, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Module\Manager $moduleManager, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $escaper, $scopeConfig, $messagingFactory, $vendorFactory, $transportBuilder, $inlineTranslation, $moduleManager, $date, $storeManager, $customerRepositoryInterface, $resultJsonFactory, $formKeyValidator);
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
