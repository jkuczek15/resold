<?php
namespace Resold\Api\Controller\Product\Index;

/**
 * Interceptor class for @see \Resold\Api\Controller\Product\Index
 */
class Interceptor extends \Resold\Api\Controller\Product\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Ced\CsMarketplace\Model\VendorFactory $Vendor, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Catalog\Helper\Image $_image, \Magento\Framework\App\Filesystem\DirectoryList $directoryList, \Magento\Framework\Filesystem\Io\File $file)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $resultJsonFactory, $formKeyValidator, $categoryFactory, $Vendor, $transportBuilder, $inlineTranslation, $customerRepository, $_image, $directoryList, $file);
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
